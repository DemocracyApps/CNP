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
        $map = $this->runDriver['map'] = array();
        dd($inputSpec);
        for ($i = 0, $size = count($inputSpec['map']); $i<$size; ++$i) {
            $item = $input['map'][$i];
            $item['prev'] = ($i>0)?$item[$i-1]:null;
            $item['next'] = ($i == $size-1)?null:$item[$i-1];
            $item['pagebreak'] = false;
            if (array_key_exists('tag', $item)) {
                $tag = $item['tag'];
                $map[$tag] = $item;
                if ($map['current'] == null) $map['current'] = $tag;
                if ($map['start'] == null)   $map['start'] = $tag;

                // If next item is contingent, set
                //    $item['pagebreak'] = true;
            }
            else {
                if ($item['type'] == 'pagebreak') {
                    if ($item['prev']) $item['prev']['pagebreak'] = true;
                }
            }
        }
        dd($this->runDriver);
        $this->driver = json_encode($this->runDriver);
    }
}

