<?php
namespace DemocracyApps\CNP\Inputs;
/**
 * This class is generated from an 'auto-interactive' input
 */
class CollectorAutoInputter extends \Eloquent {
    protected $table = 'collector_auto_inputs';
    protected $runDriver = null;
    private $done = false;
    private $pageBreak = false;

    public function reInitialize()
    {
        $this->runDriver = json_decode($this->driver, true);
        $this->done = $this->runDriver['done'];
    }
    public function initialize($inputSpec) 
    {
        $this->userid = \Auth::user()->getId();
        $this->expires = date('Y-m-d H:i:s', time() + 24 * 60 * 60);

        $this->runDriver = array();
        $this->runDriver['start'] = $this->runDriver['current'] = null;
        $this->runDriver['done'] = false;
        $this->runDriver['expecting'] = null;
        $this->runDriver['map'] = array();
        $previous = null;

        for ($i = 0, $size = count($inputSpec['map']); $i<$size; ++$i) {
            $item = $inputSpec['map'][$i];
            $item['prev'] = $item['next'] = null;
            $item['pagebreak'] = false;

            if (array_key_exists('id', $item)) {
                $id = $item['id'];

                // If this is the first real item, set start to it.
                if ($this->runDriver['start'] == null)   $this->runDriver['start']   = $id;

                if ($previous) {
                    $previous['next'] = $id;
                    $item['prev'] = $previous['id'];
                }

                $this->runDriver['map'][$id] = $item;
                $previous = &$this->runDriver['map'][$id];

                $item['value'] = null;

                // If next item is contingent, set
                //    $item['pagebreak'] = true;
            }
            else {
                if ($item['type'] == 'pagebreak') {
                    if ($previous) $previous['pagebreak'] = true;
                }
            }
        }
    }

    public function inputDone ()
    {
        return $this->done;
    }

    public function cleanupAndSave()
    {
        $this->driver = json_encode($this->runDriver);
        $this->save();
    }

    public function extractSubmittedValues($input)
    {
        foreach ($this->runDriver['expecting'] as $item) {
            $map = &$this->runDriver['map'];
            $map[$item]['value'] = $input[$item];
        }
        $this->runDriver['expecting'] = array();
    }
    public function getNextInput()
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
        $data = &$this->runDriver;

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
            if ($result['next']) {
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
        if ($result != null && $result['pagebreak']) $this->pageBreak = true;
        return $result;
    }
}

