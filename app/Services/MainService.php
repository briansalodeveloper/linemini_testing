<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Traits\ModelCollectionTrait;

class MainService
{
    use ModelCollectionTrait;

    /*======================================================================
     * PROPERTIES
     *======================================================================*/

    /**
     * @var [RepositoryInterface]
     */
    public $repository;

    /**
     * @var
     */
    public $temporaryResources = [];

    /*======================================================================
     * CONSTANTS
     *======================================================================*/

    const RESOURCETYPE_IMAGE = 'image';

    /*======================================================================
     * METHODS
     *======================================================================*/

    /**
     * add temporary resources to list
     *
     * @param String $type
     * @param String/Int/Object $value
     * @param Array $parameters
     * @return void
     */
    public function tmpResourcesAdd(string $type, $value, $parameters = [])
    {
        $tmp = $this->temporaryResources;

        if (!empty($value)) {
            if ($type == self::RESOURCETYPE_IMAGE) {
                if (!is_array($value)) {
                    $value = [$value];
                }
                
                if (count($value) != 0) {
                    if (empty($tmp[self::RESOURCETYPE_IMAGE])) {
                        $tmp[self::RESOURCETYPE_IMAGE] = [];
                    }

                    foreach ($value as $val) {
                        $tmp[self::RESOURCETYPE_IMAGE][] = [
                            'value' => $val,
                            'parameters' => $parameters,
                        ];
                    }
                }
            }
        }

        $this->temporaryResources = $tmp;
    }

    /**
     * dump temporary resources from list
     *
     * @return void
     */
    public function tmpResourcesDump()
    {
        $tmp = $this->temporaryResources;

        foreach ($tmp as $type => $resources) {
            if ($type == self::RESOURCETYPE_IMAGE) {
                foreach ($resources as $ind => $resource) {
                    $value = $resource['value'];
                    $parameters = $resource['parameters'];
                    $hasDisk = !empty($parameters['disk']);

                    if ($hasDisk && (Upload::isUrlPublic($value) || Upload::isUrlOwnedS3($value))) {
                        $disk = $parameters['disk'];
                        Upload::removeFromUrl($value, $disk);
                    } else {
                        Upload::removeFromUrl($value);
                    }
                }
            }
        }
    }
}
