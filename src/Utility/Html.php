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

    public static function spaces($count) {
        $s = "";
        for ($i=0; $i<$count; ++$i) {
            $s .= " ";
        }
        return $s;
    }

    static public function createSelfClosingElement($type, $spaces=5)
    {
        if ( ! array_key_exists($type, self::$selfClosingElements)) {
            throw new \Exception("Attempt to create invalid self-closing HTML element " . $type);
        }
        echo Self::spaces($spaces) . "<".$type.">\n";
    }

    static public function createElement($type, $content, $properties, $spaces=5)
    {
        echo Self::spaces($spaces) . "<".$type;
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
        echo "\n";
    }

    static public function startElement($type, $properties, $spaces=5)
    {
        echo Self::spaces($spaces) . "<".$type;
        foreach ($properties as $key => $value) {
         echo " ". $key . "=\"".$value."\"";
        }
        echo ">\n";
    }

    static public function endElement($type, $spaces=5)
    {
        echo Self::spaces($spaces) . "</".$type.">\n";
    }
}
