<?php

namespace App\Repositories;

use DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\MainRepositoryInterface;
use App\Interfaces\MessageRepositoryInterface;
use App\Models\Message;

class MessageEloquentRepository extends MainEloquentRepository implements MainRepositoryInterface, MessageRepositoryInterface
{
    /*======================================================================
     * PROPERTIES
     *======================================================================*/

    /**
     * @var Message $Model
     */
    public $Model = Message::class;

    /*======================================================================
     * CUSTOM METHODS
     *======================================================================*/

    /**
     * acquire all Message records
     *
     * @param Int $paginatePerPage
     * @return LengthAwarePaginator
     */
    public function acquireAll($paginatePerPage = 10): LengthAwarePaginator
    {
        $rtn = $this->arrayToPagination([]);

        try {
            $query = $this->Model::whereNotDeleted();
    
            $rtn = $query->sortDesc()->paginate($paginatePerPage);
        } catch (\Exception $e) {
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Exception: ' . $e->getMessage());
        } catch (\Error $e) {
            \L0g::error($e->getMessage());
            \SlackLog::error($e->getMessage());
        }

        return $rtn;
    }

    /**
     * acquire a Message record
     *
     * @param Int $id
     * @return Message
     */
    public function acquire($id)
    {
        return parent::acquire($id);
    }

    /**
     * add a Message record
     *
     * @param Array $attributes
     * @return Bool/Message
     */
    public function add(array $attributes)
    {
        return parent::add($attributes);
    }

    /**
     * adjust a Message record
     *
     * @param Int $id
     * @param Array $attributes
     * @return Bool/Message
     */
    public function adjust(int $id, array $attributes)
    {
        return parent::adjust($id, $attributes);
    }

    /**
     * annul a Message record
     *
     * @param Int $id
     * @return Bool
     */
    public function annul(int $id)
    {
        return parent::annul($id);
    }

    /**
     * acquire scheduled to be send records
     *
     * @return Collection $rtn
     */
    public function acquireScheduledToBeSendNow()
    {
        $rtn = false;

        try {
            $query = $this->Model::whereNotDeleted()
                ->whereNotSend()
                ->whereDate('sendDateTime', '<=', Carbon::now());

            $rtn = $query->get();
        } catch (\Exception $e) {
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Exception: ' . $e->getMessage());
        } catch (\Error $e) {
            \L0g::error($e->getMessage());
            \SlackLog::error($e->getMessage());
        }

        return $rtn;
    }
}
