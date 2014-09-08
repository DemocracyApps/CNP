<?php
namespace DemocracyApps\CNP\Utility;

class Html {

    private static $selfClosingElements = array(
        'area' => true,
        'base' => true,
        'br' => true,
        'col' => true,
        'command' => true,
        'embed' => true,
        'hr' => true,
        'img' => true,
        'input' => true,
        'keygen' => true,
        'link' => true,
        'meta' => true,
        'param' => true,
        'source' => true,
        'track' => true,
        'wbr' => true
        );

    static function createSelfClosingElement($type)
    {
        if ( ! array_key_exists($type, self::$selfClosingElements)) {
            throw new \Exception("Attempt to create invalid self-closing HTML element " . $type);
        }
        echo "<".$type.">";
    }

    static function createElement($type, $content, $properties)
    {
        echo "<".$type;
        foreach ($properties as $key => $value) {
         echo " ". $key . "=\"".$value."\"";
        }
        if ($content) {
         echo ">";
         echo $content;
         echo "</".$type.">";
        }
        else {
        echo ">";
        echo "</".$type.">";
        }
    }

    static function startElement($type, $properties)
    {
        echo "<".$type;
        foreach ($properties as $key => $value) {
         echo " ". $key . "=\"".$value."\"";
        }
        echo ">";
    }

    static function endElement($type)
    {
        echo "</".$type.">";
    }

    static function createInput($desc)
    {
        self::startElement("div", array('class' => 'form-group'));
        self::createElement("label", $desc['prompt'], array('for' => $desc['id']));
        if ($desc['type'] == 'text') {
         self::createElement('input', null, array('class' => 'form-control', 'name' => $desc['id'], 'type'=>'text'));
        }
        elseif ($desc['type'] == 'textarea') {
         self::createElement('textarea', null, array('class' => 'form-control', 'name' => $desc['id'],
                       'cols'=>'50', 'rows' => '10'));
        }
        self::endElement("div");
    }
}
