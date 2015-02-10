<?php

use DemocracyApps\CNP\Graph\Element;
use DemocracyApps\CNP\Graph\Relation;
use DemocracyApps\CNP\Graph\RelationType;

Route::get('/kumu', array('as' => 'kumu', function ()
{
    $project = \Input::get('project');
    $file= public_path(). "/downloads/kumu1.csv";
    $fptr = fopen($file, "w");
    $line = "Label,Type,Description\n";
    fwrite($fptr,$line);
    \Log::info("All project elements");
    $elements = Element::getProjectElements($project);
    \Log::info("Now write");
    foreach($elements as $d) {
        $line = $d->id . "," . CNP::getElementTypeName($d->type) . ",\"" . $d->name . "\"\n";
        fwrite($fptr,$line);
    }
    $line = "\n";
    fwrite($fptr,$line);
    $line = "\n";
    fwrite($fptr,$line);
    $line = "From,To,Type\n";
    fwrite($fptr,$line);
    $total = Relation::countProjectRelations($project);
    $batches = intval( $total/1000 );
    if ($batches * 1000 < $total) ++$batches;
    \Log::info("Now relations - " . $total . " with " . $batches . " groups of 1000");

    for ($i=0; $i<$batches; ++$i) {
        $start = $i * 1000;

        $relations = Relation::getProjectRelationsPaged($project, $start, 1000);
        \Log::info("Relations " . $i);
        $relationsTypesMap = RelationType::getRelationTypesMap();
        foreach($relations as $d) {
            $line = $d->fromId . "," . $d->toId . "," . $relationsTypesMap[$d->relationId] . "\n";
            fwrite($fptr,$line);
        }
    }
    fclose($fptr);
    $headers = array(
        'Content-Type: text/csv',
    );
    return Response::download($file, 'kumu1.csv', $headers);

}));

Route::get('/download', function ()
{
    $file= public_path(). "/downloads/export.csv";
    $headers = array(
        'Content-Type: text/csv',
    );
    return Response::download($file, 'export.csv', $headers);

});
