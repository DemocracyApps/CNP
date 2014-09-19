<?php namespace DemocracyApps\CNP\Views\menus;

class MenuGenerator {

    public static function generateMenu($currentMode)
    {
        $mode = \Session::get('cnpMode');
        $menu = self::loadMenu($mode);
        //dd($menu);
        //echo "<b>" . $menu['title'] . "</b>\n";
        return $menu;
    }
   
    private static function loadMenu($menuId)
    {
        \Log::info("Loading menus");
        $fileName = base_path()."/src/Views/menus/navigation_menus.json";
        $str = file_get_contents($fileName);
        $str = json_minify($str);
        $menus = json_decode($str, true);
        //dd($menus['menus'][$menuId]);
        if (! array_key_exists($menuId, $menus['menus'])) throw new \Exception("Menu ".$menuId." not found");
        return $menus['menus'][$menuId];
    }
}