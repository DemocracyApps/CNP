<?php 
use \DemocracyApps\CNP\Entities as DAEntity;

Route::get('/kumu', array('as' => 'kumu', function ()
{
    $project = \Input::get('project');
    $file= public_path(). "/downloads/kumu1.csv";
    $fptr = fopen($file, "w");
    $line = "Label,Type,Description\n";
    fwrite($fptr,$line);
    \Log::info("All project elements");
    $elements = DAEntity\Element::allProjectElements($project);
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
    $total = DAEntity\Relation::countProjectRelations($project);
    $batches = intval( $total/1000 );
    if ($batches * 1000 < $total) ++$batches;
    \Log::info("Now relations - " . $total . " with " . $batches . " groups of 1000");

    for ($i=0; $i<$batches; ++$i) {
        $start = $i * 1000;

        $relations = DAEntity\Relation::getProjectRelationsPaged($project, $start, 1000);
        \Log::info("Relations " . $i);    
        $relationsTypesMap = DAEntity\Eloquent\RelationType::getRelationTypesMap();
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


