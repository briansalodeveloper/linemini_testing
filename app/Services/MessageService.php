<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\API\Line\PushMessage;
use App\Helpers\Trumbowyg;
use App\Helpers\Upload;
use App\Interfaces\MessageRepositoryInterface;
use App\Services\MainService;
use App\Models\Message;
use App\Models\UnionLine;

class MessageService extends MainService
{
    /*======================================================================
     * PROPERTIES
     *======================================================================*/

    /*======================================================================
     * CONSTRUCTOR
     *======================================================================*/

    /**
     * @param MessageRepositoryInterface $repository
     */
    public function __construct(MessageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /*======================================================================
     * PUBLIC METHODS
     *======================================================================*/

    /**
     * fetch all records
     *
     * @return Array $rtn
     */
    public function all(): array
    {
        $rtn = [
            'data' => $this->repository->acquireAll()
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
            'aoList' =>  Message::AFFILIATION_OFFICE_LIST,
            'ubList' =>  Message::UTILIZATION_BUSINESS_LIST,
            'storeList' =>  Message::STORE_LIST,
        ];

        return $rtn;
    }

    /**
     * store a record
     *
     * @return Bool $rtn
     */
    public function store()
    {
        $rtn = false;

        DB::beginTransaction();
        try {
            $data = [
                'sendTargetFlg' => request()->get('sendTargetFlg'),
                'sendDateTime' => request()->get('sendDateTime'),
                'messageName' => request()->get('messageName') . (request()->has('cp') ? ' - ' . __('words.Copy') : ''),
                'thumbnail' => request()->get('thumbnail'),
                'contents' => request()->get('contents'),
                'ubId' => request()->get('ubId', 0),
                'aoId' => request()->get('aoId', 0),
                'storeId' => request()->get('storeId', 0),
            ];

            $message = $this->repository->NTCadd($data);

            if ($message) {
                $kumicd = $this->getCSVUnionMemberId(request()->get('unionMemberCsv'));
                $thumbnails = $this->storeThumbnailUrlToS3(request()->get('thumbnail'));
                $contents = $this->storeContentsUrlToS3(request()->get('contents'));

                if (request()->has('cp')) {
                    $copyFrom = $this->repository->NTCacquire(request()->get('messageId'));
                    
                    if (empty($kumicd)) {
                        $kumicd = $copyFrom->kumicd;
                    }
    
                    $message->sendFlg = Message::SENDSATTUS_NO;
                }

                if (!empty($kumicd)) {
                    $message->kumicd = $kumicd;
                }

                if ($thumbnails) {
                    $message->thumbnail = $thumbnails['main'];
                    $message->thumbnailPreview = $thumbnails['preview'];
                }

                if ($contents) {
                    $message->contents = $contents;
                }

                if ($thumbnails || $contents) {
                    $rtn = $this->repository->NTCadjust($message->id, [
                        'sendFlg' => $message->sendFlg,
                        'kumicd' => $message->getAttr('kumicd', ''),
                        'thumbnail' => $message->thumbnail,
                        'thumbnailPreview' => $message->thumbnailPreview,
                        'contents' => $message->contents,
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
     * @return Bool $rtn
     */
    public function update(int $id)
    {
        $rtn = false;

        DB::beginTransaction();
        try {
            $oldData = $this->repository->NTCacquire($id);

            $data = [
                'sendTargetFlg' => request()->get('sendTargetFlg'),
                'sendDateTime' => request()->get('sendDateTime'),
                'messageName' => request()->get('messageName'),
                'thumbnail' => request()->get('thumbnail'),
                'contents' => request()->get('contents'),
                'ubId' => request()->get('ubId', 0),
                'aoId' => request()->get('aoId', 0),
                'storeId' => request()->get('storeId', 0),
            ];

            if ($oldData->sendDateTime != $data['sendDateTime'] && Carbon::parse($oldData->sendDateTime) < Carbon::parse($data['sendDateTime'])) {
                $data['sendFlg'] = Message::SENDSATTUS_NO;
            }

            $message = $this->repository->NTCadjust($id, $data);

            if ($message) {
                $kumicd = $this->getCSVUnionMemberId(request()->get('unionMemberCsv'));
                $thumbnails = $this->storeThumbnailUrlToS3(request()->get('thumbnail'));
                $contents = $this->storeContentsUrlToS3(request()->get('contents'));

                if (!empty($kumicd)) {
                    $message->kumicd = $kumicd;
                } else {
                    $message->kumicd = request()->get('kumicd', '');
                }

                if ($thumbnails) {
                    $message->thumbnail = $thumbnails['main'];
                    $message->thumbnailPreview = $thumbnails['preview'];
                }

                if ($contents) {
                    $message->contents = $contents;
                }

                if ($thumbnails || $contents) {
                    $rtn = $this->repository->NTCadjust($message->id, [
                        'kumicd' => $message->kumicd,
                        'thumbnail' => $message->thumbnail,
                        'thumbnailPreview' => $message->thumbnailPreview,
                        'contents' => $message->contents,
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

    /**
     * send the message to line API messaging
     *
     * @param Message $message
     * @param Bool $isChangeStatusToSend
     * @return Array $rtn
     */
    public function send(Message $message, bool $isChangeStatusToSend = true)
    {
        $messageCntSuccess = 0;
        $messageCntFailed = 0;
        $messageCntNoLineUsers = 0;
        $unionLineIdList = [];

        try {
            $lineIds = [];
            $isValidData = true;
            $kumicd = null;
            $ubId = null;
            $aoId = null;
            $storeId = null;
            $sendSuccess = false;

            if ($message->isTargetAll) {
                //
            } elseif ($message->isTargetUm) {
                $kumicd = $message->kumicd;
            } elseif ($message->isTargetUb) {
                $ubId = $message->ubId;
            } elseif ($message->isTargetAo) {
                $aoId = $message->aoId;
            } elseif ($message->isTargetSt) {
                $storeId = $message->storeId;
            } else {
                $isValidData = false;
            }

            if ($isValidData) {
                $query = UnionLine::whereNotDeleted()->where(function ($query) use ($kumicd, $ubId, $aoId, $storeId) {
                    if (!is_null($kumicd) || !is_null($ubId) || !is_null($aoId)) {
                        $query->whereHas('unionMember', function ($query) use ($kumicd, $ubId, $aoId) {
                            if (!is_null($kumicd)) {
                                $query->where('unionMemberCode', $kumicd);
                            } elseif (!is_null($ubId)) {
                                $query->where(function ($query) use ($ubId) {
                                    $query->where('utilizationBusiness' . $ubId, 1);
                                });
                            } elseif (!is_null($aoId)) {
                                $query->where(function ($query) use ($aoId) {
                                    $query->where('affiliationOffice', $aoId);
                                });
                            }
            
                            return $query;
                        });
                    } elseif (!is_null($storeId)) {
                        $query->whereHas('flyerStore', function ($query) use ($storeId) {
                            $query->isView();
    
                            if (!is_null($storeId)) {
                                $query->where('storeId', $storeId);
                            }
            
                            return $query;
                        });
                    }
    
                    return $query;
                });
    
                if (!is_null($storeId)) {
                    $query = $query->groupBy('LineTokenId');
                }
    
                $list = $query->get();
                $lineIds = $list->pluck('LineTokenId')->toArray();

                if (count($lineIds) != 0) {
                    $url = $message->thumbnail;
                    $urlPreview = $message->thumbnailPreview;

                    $fileName = Upload::getBaseName($url);
                    $fileNamePreview = Upload::getBaseName($urlPreview);

                    $url = explode($fileName, $url)[0];
                    $urlPreview = explode($fileNamePreview, $urlPreview)[0];

                    $fileName = urlencode($fileName);
                    $fileNamePreview = urlencode($fileNamePreview);

                    $url .= $fileName;
                    $urlPreview .= $fileNamePreview;
                    $contents = str_replace('<br>', "\n", $message->contents);
                    $contents = str_replace('<br/>', "\n", $contents);
                    $contents = str_replace('<br />', "\n", $contents);
                    $contents = str_replace('</p>', "\n", $contents);
                    $contents = str_replace('&nbsp;', " ", $contents);
                    
                    if (strpos($contents, "\n", -1) !== false) {
                        $contents = substr($contents, 0, strpos($contents, "\n", -1));
                    }

                    $contents = strip_tags($contents);
                    $lineIds = array_chunk($lineIds, PushMessage::MAX_SEND_USER_COUNT);

                    $sendSuccess = true;
                    foreach ($lineIds as $ids) {
                        $rtnSend = PushMessage::send($ids, $contents, $url, $urlPreview);

                        if (!$rtnSend) {
                            $sendSuccess = false;
                        }
                    }
                }
            }

            $unionLineIdList = $lineIds;

            if ($sendSuccess || count($lineIds) == 0) {
                if ($isChangeStatusToSend) {
                    $this->repository->NTCadjust($message->id, [
                        'sendFlg' => Message::SENDSATTUS_YES
                    ]);
                }
            }

            if ($sendSuccess) {
                $messageCntSuccess++;
            } elseif (count($lineIds) == 0) {
                $messageCntNoLineUsers++;
            } else {
                $messageCntFailed++;
            }
        } catch (\Exception $e) {
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Exception: ' . $e->getMessage());
        } catch (\Error $e) {
            \L0g::error($e->getMessage());
            \SlackLog::error($e->getMessage());
        }

        $rtn = [
            'messageCntSuccess' => $messageCntSuccess,
            'messageCntFailed' => $messageCntFailed,
            'messageCntNoLineUsers' => $messageCntNoLineUsers,
            'unionLineIdList' => $unionLineIdList,
        ];

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
     * @return Bool $rtn
     */
    private function storeThumbnailUrlToS3(string $thumbnailUrl)
    {
        $rtn = false;
        $path = Upload::getCustomPath('messageThumbnail');
        $name = Upload::getBaseName($thumbnailUrl);

        if (Upload::isUrlPublic($thumbnailUrl)) {
            $saved = Upload::saveFromUrl($thumbnailUrl, $path, $name, null, Upload::DISK_S3);

            if ($saved) {
                $name = 'preview-' . $name;
                $savedPreview = Upload::saveFromUrl($thumbnailUrl, $path, $name, null, Upload::DISK_S3);

                if ($savedPreview) {
                    $rtn = [
                        'main' => $saved,
                        'preview' => $savedPreview
                    ];
                    $this->tmpResourcesAdd(self::RESOURCETYPE_IMAGE, $thumbnailUrl);
                }
            }
        }

        return $rtn;
    }

    /**
     * store contents url to s3
     *
     * @param String $contents
     * @return Bool $rtn
     */
    private function storeContentsUrlToS3(string $contents)
    {
        $rtn = false;

        if ($contents) {
            $rtn = Trumbowyg::moveTemporaryFiles(
                $contents,
                Upload::getCustomPath('messageImageContent'),
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
