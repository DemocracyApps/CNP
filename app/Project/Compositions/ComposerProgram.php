<?php
namespace DemocracyApps\CNP\Project\Compositions;

class ComposerProgram {
    protected $runnable = null;
    protected $program = null;
    private $done = false;
    private $pageBreak = false;

    public function restart($savedState, $start = null) {
        $this->runnable = json_decode($savedState, true);
        $this->done = $this->runnable['done'];
        // No idea what to do with start here, actually.
    }

    public function compile($program, $doingInput, $start = null)
    {
        $this->program = $program; // Just in case.
        $this->runnable = array();
        $this->runnable['start'] = $start;
        $this->runnable['current'] = null;
        $this->runnable['done'] = false;
        $this->runnable['expecting'] = null;
        $this->runnable['map'] = array();
        $previous = null;
        if (array_key_exists('columnMap', $program['map'])) {
            $inputMap = &$program['map']['columnMap'];
        }
        else {
            $inputMap = &$program['map'];
        }
        $breakSequence = false;
        for ($i = 0, $size = count($inputMap); $i<$size; ++$i) {
            $item = $inputMap[$i];
            $item['prev'] = null;
            if (!array_key_exists('next', $item)) $item['next'] = null;
            $item['pagebreak'] = false;

            if (array_key_exists('use', $item)) {
                if ($item['use'] == 'break' || $item['use'] == 'pagebreak') {
                    if ($item['use'] == 'pagebreak') {
                        $doit = true;
                        if (array_key_exists('suppress', $item)) {
                            if ($doingInput && $item['suppress'] == 'input') $doit = false;
                            if (! $doingInput && $item['suppress'] == 'output') $doit = false;
                        }
                        if ($doit && $previous) $previous['pagebreak'] = true;
                    }
                    elseif ($item['use'] == 'break') {
                        if ($this->runnable['start'] != null) { // We just ignore stop elements at the beginning.
                            $breakSequence = true; // This will suppress next/prev calculation above
                        }

                    }
                }
                else if (array_key_exists('inputType', $item) && $item['inputType'] == 'auto') {
                    // Nothing!
                }
                else  {
                    if (array_key_exists('id', $item)) {
                        $id = $item['id'];
                    }
                    else {
                        $id = '__' . $i . '__';
                        $item['id'] = $id;
                    }
                    // If this is the first real item, set start to it.
                    if ($this->runnable['start'] == null) $this->runnable['start']   = $id;

                    if ($previous && ! $breakSequence) { // A break element breaks connection
                        if ( ! $previous['next'] ) {
                            $previous['next'] = $id; // don't override
                        }
                        $item['prev'] = $previous['id'];
                    }
                    if ($item['use'] == 'decision') {
                        $item['next'] = '!value';
                    }
                    $breakSequence = false;

                    $this->runnable['map'][$id] = $item;
                    $previous = &$this->runnable['map'][$id];

                    $item['value'] = null;

                    // If next item is contingent, set
                    //    $item['pagebreak'] = true;
                }
            }
        }
    }

    public function executionDone ()
    {
        return $this->done;
    }

    public function getProgramState()
    {
        return json_encode($this->runnable);
    }

    public function getExpectedIds()
    {
        $expecting = $this->runnable['expecting'];
        $this->runnable['expecting'] = array();
        return $expecting;
    }

    public function &getExpectedCompositionElement($id)
    {
        $map = &$this->runnable['map'];
        return $map[$id];
    }

    public function getCompositionElementById($id)
    {
        if (array_key_exists($id, $this->runnable['map'])) {
            return $this->runnable['map'][$id];
        }
        else {
            return null;
        }
    }

    public function getNext($doingInput = true)
    {
        /*
         * At the top of the routine, $current is the page we were most recently on. We don't advance
         * when we return it because sometimes we'll need to know their response to something before
         * we can decide where to go next. So a call to this function, getNextInput(), means that we 
         * are now ready to proceed to the next element. We figure it out, store it in $current and
         * return it. 
         *
         * Returning null means that we are done, at least for this section of inputs.
         *
         * In the simplest case, the sequence is just a series of elements with next pointers (and maybe 
         * some pagebreaks in between). At the beginning $current is null.
         * 
         */
        $data = &$this->runnable;
        if ($this->pageBreak) {
            return null; 
        }

        $current = $data['current'];
        if ($current == null) { // We are at the beginning
            $data['current'] = $current = $data['start'];
            $result = $data['map'][$current];
            $data['expecting'] = array($current);
        }
        else {
            $result = $data['map'][$current];
            if (array_key_exists('use', $result) && $result['use'] == 'decision') {
                if (array_key_exists('value', $result)) {
                    $data['current'] = $result['value'];
                    $data['expecting'][] = $data['current'];
                    $result = $data['map'][$result['value']];
                }
                else {
                    $result = null;
                }
            }
            else if ($result['next']) {
                $data['current'] = $result['next'];
                $data['expecting'][] = $data['current'];
                $result = $data['map'][$result['next']];
            }            
            else {
                $this->done = true;
                $data['done'] = true;
                $result = null;
            }
        }
        if ($result != null && $result['pagebreak']) {
            $this->pageBreak = true;
        }
        return $result;
    }
}