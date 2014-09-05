<?php
namespace DemocracyApps\CNP\Entities\Eloquent;
/**
 * This class is generated from an 'auto-interactive' input
 */
class CollectorAutoInputter extends \Eloquent {
    protected $table = 'collector_auto_inputs';
    protected $runDriver = null;

    public function initialize($inputSpec) 
    {
        $this->userid = \Auth::user()->getId();
        $this->expires = date('Y-m-d H:i:s', time() + 24 * 60 * 60);

        $this->runDriver = array();
        $this->runDriver['start'] = $this->runDriver['current'] = null;

        $this->runDriver['map'] = array();
        $previous = null;

        for ($i = 0, $size = count($inputSpec['map']); $i<$size; ++$i) {
            $item = $inputSpec['map'][$i];
            $item['prev'] = $item['next'] = null;
            $item['pagebreak'] = false;

            if (array_key_exists('tag', $item)) {
                $ptag = null;
                $tag = $item['tag'];

                // If this is the first real item, set start AND current to it.
                if ($this->runDriver['current'] == null) $this->runDriver['current'] = $tag;
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
        $this->driver = json_encode($this->runDriver);
    }
}

