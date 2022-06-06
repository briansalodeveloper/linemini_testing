<?php

namespace App\Services;

use DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Trumbowyg;
use App\Helpers\Upload;
use App\Interfaces\ContentPlanRepositoryInterface;
use App\Interfaces\DisplayTargetContentRepositoryInterface;
use App\Interfaces\DisplayTargetContentAORepositoryInterface;
use App\Interfaces\DisplayTargetContentUBRepositoryInterface;
use App\Services\MainService;
use App\Models\ContentPlan;
use App\Models\DisplayTargetContent;
use App\Models\DisplayTargetContentAO;
use App\Models\DisplayTargetContentUB;

class ContentPlanService extends MainService
{
    /*======================================================================
     * PROPERTIES
     *======================================================================*/

    /**
     * @var DisplayTargetContentRepositoryInterface
     */
    private $contentPlanRepository;

    /**
     * @var DisplayTargetContentAORepositoryInterface
     */
    private $contentPlanAORepository;

    /**
     * @var DisplayTargetContentUBRepositoryInterface
     */
    private $contentPlanUBRepository;

    /*======================================================================
     * CONSTRUCTOR
     *======================================================================*/

    /**
     * @param ContentPlanRepositoryInterface $repository
     */
    public function __construct(
        ContentPlanRepositoryInterface $repository,
        DisplayTargetContentRepositoryInterface $contentPlanRepository,
        DisplayTargetContentAORepositoryInterface $contentPlanAORepository,
        DisplayTargetContentUBRepositoryInterface $contentPlanUBRepository
    ) {
        $this->repository = $repository;
        $this->contentPlanRepository = $contentPlanRepository;
        $this->contentPlanAORepository = $contentPlanAORepository;
        $this->contentPlanUBRepository = $contentPlanUBRepository;
    }

    /*======================================================================
     * PUBLIC METHODS
     *======================================================================*/

    /**
     * fetch all records
     *
     * @param Int $type
     * @return Array $rtn
     */
    public function all(int $type): array
    {
        $rtn = [
            'data' => $this->repository->acquireAll($type)
        ];

        return $rtn;
    }

    /**
     * fetch a record
     *
     * @param Int|Null $id
     * @return Array $rtn
     */
    public function get(int $id = null): array
    {
        $rtn = [
            'data' => $this->repository->acquire($id),
            'aoList' =>  DisplayTargetContentAO::AFFILIATION_OFFICE_LIST,
            'ubList' =>  DisplayTargetContentUB::UTILIZATION_BUSINESS_LIST,
        ];

        return $rtn;
    }

    /**
     * store a record
     *
     * @param Int $type
     * @return Bool $rtn
     */
    public function store(int $type)
    {
        $rtn = false;

        DB::beginTransaction();
        try {
            $typeStr = '';

            if ($type == ContentPlan::CONTENTTYPE_NOTICE) {
                $typeStr = 'notice';
            } elseif ($type == ContentPlan::CONTENTTYPE_RECIPE) {
                $typeStr = 'recipe';
            } elseif ($type == ContentPlan::CONTENTTYPE_PRODUCTINFO) {
                $typeStr = 'productInformation';
            } elseif ($type == ContentPlan::CONTENTTYPE_COLUMN) {
                $typeStr = 'column';
            }

            $data = [
                'contentType' => $type,
                'contentTypeNews' => request()->get('contentTypeNews'),
                'displayTargetFlg' => request()->get('displayTargetFlg'),
                'startDateTime' => request()->get('startDateTime'),
                'endDateTime' => request()->get('endDateTime'),
                'openingLetter' => request()->get('openingLetter') . (request()->has('cp') ? ' - ' . __('words.Copy') : ''),
                'openingImg' => request()->get('openingImg'),
                'contents' => request()->get('contents'),
            ];

            $contentPlan = $this->repository->NTCadd($data);

            if ($contentPlan) {
                $unionMemberId = $this->getCSVUnionMemberId(request()->get('unionMemberCsv'));
                $ubId = request()->get('utilizationBusiness');
                $aoId = request()->get('affiliationOffice');

                if (request()->has('cp')) {
                    $copyFrom = $this->repository->NTCacquire(request()->get('contentPlanId'));

                    if (empty($unionMemberId) && !empty($copyFrom->displayTarget)) {
                        $unionMemberId = $copyFrom->displayTarget->kumicd;
                    }
                }

                if (!empty($unionMemberId)) {
                    $this->contentPlanRepository->NTCadd([
                        'contentPlanId' => $contentPlan->id,
                        'kumicd' => $unionMemberId,
                    ]);
                }

                if (!empty($aoId)) {
                    $this->contentPlanAORepository->NTCadd([
                        'contentPlanId' => $contentPlan->id,
                        'affiliationOfficeId' => $aoId,
                    ]);
                }

                if (!empty($ubId)) {
                    $this->contentPlanUBRepository->NTCadd([
                        'contentPlanId' => $contentPlan->id,
                        'utilizationBusinessId' => $ubId,
                    ]);
                }

                $thumbnail = $this->storeThumbnailUrlToS3(request()->get('openingImg'), $typeStr);
                $contents = $this->storeContentsUrlToS3(request()->get('contents'), $typeStr);

                if ($thumbnail) {
                    $contentPlan->openingImg = $thumbnail;
                }

                if ($contents) {
                    $contentPlan->contents = $contents;
                }

                if ($thumbnail || $contents) {
                    $rtn = $this->repository->NTCadjust($contentPlan->id, [
                        'openingImg' => $contentPlan->openingImg,
                        'contents' => $contentPlan->contents,
                    ]);
                } else {
                    $rtn = true;
                }
            }

            if ($rtn) {
                DB::commit();
                $this->tmpResourcesDump();
            } else {
                DB::rollback();
            }
        } catch (\Exception $e) {
            DB::rollback();
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Exception: ' . $e->getMessage());
        } catch (\Error $e) {
            DB::rollback();
            \L0g::error($e->getMessage());
            \SlackLog::error($e->getMessage());
        }

        return $rtn;
    }

    /**
     * update a record
     *
     * @param Int $id
     * @param Int $type
     * @return Bool $rtn
     */
    public function update(int $id, int $type)
    {
        $rtn = false;

        DB::beginTransaction();
        try {
            $typeStr = '';

            if ($type == ContentPlan::CONTENTTYPE_NOTICE) {
                $typeStr = 'notice';
            } elseif ($type == ContentPlan::CONTENTTYPE_RECIPE) {
                $typeStr = 'recipe';
            } elseif ($type == ContentPlan::CONTENTTYPE_PRODUCTINFO) {
                $typeStr = 'productInformation';
            } elseif ($type == ContentPlan::CONTENTTYPE_COLUMN) {
                $typeStr = 'column';
            }

            $data = [
                'contentType' => $type,
                'contentTypeNews' => request()->get('contentTypeNews'),
                'displayTargetFlg' => request()->get('displayTargetFlg'),
                'startDateTime' => request()->get('startDateTime'),
                'endDateTime' => request()->get('endDateTime'),
                'openingLetter' => request()->get('openingLetter'),
                'openingImg' => request()->get('openingImg'),
                'contents' => request()->get('contents')
            ];

            $contentPlan = $this->repository->NTCadjust($id, $data);

            if ($contentPlan) {
                $unionMemberId = $this->getCSVUnionMemberId(request()->get('unionMemberCsv'));
                $ubId = request()->get('utilizationBusiness');
                $aoId = request()->get('affiliationOffice');

                if (!empty(request()->get('unionMemberCsv'))) {
                    if (!empty($contentPlan->displayTarget)) {
                        $this->contentPlanRepository->NTCadjust($contentPlan->displayTarget->id, ['kumicd' => $unionMemberId]);
                    } else {
                        $this->contentPlanRepository->NTCadd([
                            'contentPlanId' => $contentPlan->id,
                            'kumicd' => $unionMemberId,
                        ]);
                    }
                } else {
                    if (!empty($contentPlan->displayTarget) && $contentPlan->displayTargetFlg != ContentPlan::DSPTARGET_UNIONMEMBER) {
                        $this->contentPlanRepository->NTCannul($contentPlan->displayTarget->id);
                    }
                }

                if (!empty($aoId)) {
                    if (!empty($contentPlan->displayTargetAO)) {
                        $this->contentPlanAORepository->NTCadjust($contentPlan->displayTargetAO->id, ['affiliationOfficeId' => $aoId]);
                    } else {
                        $this->contentPlanAORepository->NTCadd([
                            'contentPlanId' => $contentPlan->id,
                            'affiliationOfficeId' => $aoId,
                        ]);
                    }
                } else {
                    if (!empty($contentPlan->displayTargetAO)) {
                        $this->contentPlanAORepository->NTCannul($contentPlan->displayTargetAO->id);
                    }
                }

                if (!empty($ubId)) {
                    if (!empty($contentPlan->displayTargetUB)) {
                        $this->contentPlanUBRepository->NTCadjust($contentPlan->displayTargetUB->id, ['utilizationBusinessId' => $ubId]);
                    } else {
                        $this->contentPlanUBRepository->NTCadd([
                            'contentPlanId' => $contentPlan->id,
                            'utilizationBusinessId' => $ubId,
                        ]);
                    }
                } else {
                    if (!empty($contentPlan->displayTargetUB)) {
                        $this->contentPlanUBRepository->NTCannul($contentPlan->displayTargetUB->id);
                    }
                }

                $thumbnail = $this->storeThumbnailUrlToS3(request()->get('openingImg'), $typeStr);
                $contents = $this->storeContentsUrlToS3(request()->get('contents'), $typeStr);

                if ($thumbnail) {
                    $contentPlan->openingImg = $thumbnail;
                }

                if ($contents) {
                    $contentPlan->contents = $contents;
                }

                if ($thumbnail || $contents) {
                    $rtn = $this->repository->NTCadjust($contentPlan->id, [
                        'openingImg' => $contentPlan->openingImg,
                        'contents' => $contentPlan->contents,
                    ]);
                } else {
                    $rtn = true;
                }
            }

            if ($rtn) {
                DB::commit();
                $this->tmpResourcesDump();
            } else {
                DB::rollback();
            }
        } catch (\Exception $e) {
            DB::rollback();
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Exception: ' . $e->getMessage());
        } catch (\Error $e) {
            DB::rollback();
            \L0g::error($e->getMessage());
            \SlackLog::error($e->getMessage());
        }

        return $rtn;
    }

    /**
     * delete a record
     *
     * @param int $id
     */
    public function destroy(int $id)
    {
        $rtn = $this->repository->annul($id);

        return $rtn;
    }

    /**
     * upload a file (image/csv)
     *
     * @param UploadedFile $file
     * @param String $fileType
     * @return Bool/String $rtn
     */
    public function upload(UploadedFile $file, string $fileType)
    {
        $rtn = false;

        if (!empty($file) && !empty($fileType)) {
            if ($fileType == 'image') {
                $fileName = $file->getClientOriginalName();
                $rtn = Upload::saveImageTemp($file, null, $fileName);
            } elseif ($fileType == 'csv') {
                $fileName = $file->getClientOriginalName();
                $rtn = Upload::saveTemp($file, null, $fileName);
            }
        }

        return $rtn;
    }

    /*======================================================================
     * PRIVATE METHODS
     *======================================================================*/

    /**
     * store csv url to s3
     *
     * @param Null/String $csvUrl
     * @return Bool $rtn
     */
    private function getCSVUnionMemberId($csvUrl)
    {
        $rtn = '';

        if ($csvUrl) {
            $basePath = Upload::getBasePath($csvUrl);
            $rootPath = Storage::disk(Upload::DISK_PUBLIC)->path($basePath);
            $csvContent = file_get_contents($rootPath);
            $rtn = explode(',', $csvContent)[0];
        }

        return $rtn;
    }

    /**
     * store thumbnail url to s3
     *
     * @param String $thumbnailUrl
     * @param String $typeStr
     * @return Bool $rtn
     */
    private function storeThumbnailUrlToS3(string $thumbnailUrl, string $typeStr)
    {
        $rtn = false;
        $path = Upload::getCustomPath($typeStr . 'Thumbnail');
        $name = Upload::getBaseName($thumbnailUrl);

        if (Upload::isUrlPublic($thumbnailUrl)) {
            $saved = Upload::saveFromUrl($thumbnailUrl, $path, $name, null, Upload::DISK_S3);

            if ($saved) {
                $rtn = $saved;
                $this->tmpResourcesAdd(self::RESOURCETYPE_IMAGE, $thumbnailUrl);
            }
        }

        return $rtn;
    }

    /**
     * store contents url to s3
     *
     * @param String $contents
     * @param String $typeStr
     * @return Bool $rtn
     */
    private function storeContentsUrlToS3(string $contents, string $typeStr)
    {
        $rtn = false;

        if ($contents) {
            $rtn = Trumbowyg::moveTemporaryFiles(
                $contents,
                Upload::getCustomPath($typeStr . 'ImageContent'),
                Upload::DISK_S3,
                false,
                false,
                true
            );

            $this->tmpResourcesAdd(self::RESOURCETYPE_IMAGE, $rtn['updatedImages']);
            $rtn = $rtn['html'];
        }

        return $rtn;
    }
}
