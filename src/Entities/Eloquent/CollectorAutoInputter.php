<?php
namespace DemocracyApps\CNP\Entities\Eloquent;
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
        $this->runDriver['map'] = array();
        $previous = null;

        for ($i = 0, $size = count($inputSpec['map']); $i<$size; ++$i) {
            $item = $inputSpec['map'][$i];
            $item['prev'] = $item['next'] = null;
            $item['pagebreak'] = false;

            if (array_key_exists('tag', $item)) {
                $ptag = null;
                $tag = $item['tag'];

                // If this is the first real item, set start to it.
                if ($this->runDriver['start'] == null)   $this->runDriver['start']   = $tag;

                if ($previous) {
                    $previous['next'] = $tag;
                    $item['prev'] = $previous['tag'];
                }

                $this->runDriver['map'][$tag] = $item;
                $previous = &$this->runDriver['map'][$tag];

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

    public function cleanupOrSave()
    {
        if ($this->done) {
            $this->driver = json_encode($this->runDriver);
            $this->save();
        }
        else {
            $this->driver = json_encode($this->runDriver);
            $this->save();
        }
    }

    public function getNextInput()
    {
        /*
         * Here's the basic philosophy ... 
         *
         * $current is the page we're on right now, but if we've just re-arrived, that's actually 
         * the page we were on last time through. The reason for this is that sometimes we'll need
         * to know their response to something before we can decide where to go next.
         *
         * So, a call to this function, getNextInput(), means that we are now ready to proceed to
         * the next element. We figure it out and return it.
         *
         * In the simplest case, the sequence is just a series of elements with next pointers.
         * At the beginning, current will be set to the head. On each call, we set $result to 
         * the element named by $current and move $current to $current['next']. If that is null,
         * we're at the end of the sequence and we should set $done to true.
         */
        $data = &$this->runDriver;
        \Log::info("In getNextInput");

        if ($this->pageBreak) {
            \Log::info("PageBreak set to true, returning null");
            return null; 
        }

        $current = $data['current'];
        if ($current == null) { // We are at the beginning
            $data['current'] = $current = $data['start'];
            $result = $data['map'][$current];
            \Log::info("Starting. Current name is " . $current);
        }
        else {
            $result = $data['map'][$current];
            if ($result['next']) {
                $data['current'] = $result['next'];
                \Log::info("Setting current page to " . $result['next']);
                $result = $data['map'][$result['next']];
            }
            else {
                \Log::info("It seems we are done");
                $this->done = true;
                $data['done'] = true;
                $result = null;
            }
        }
        if ($result != null && $result['pagebreak']) $this->pageBreak = true;
        return $result;
    }
}

