<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\API\Line\PushMessage;
use App\Http\Requests\MessageRequest;
use App\Services\MessageService;

class MessageController extends Controller
{
    /**
     * @param MessageService $service
     * @return void
     */
    public function __construct(MessageService $service)
    {
        $this->service = $service;
    }

    /**
     * message list
     *
     * @return View
     */
    public function index(): View
    {
        $data = $this->service->all();
        return view('page.message.index')->with($data);
    }

    /**
     * message create form
     * @return View
     */
    public function create(): View
    {
        return $this->edit();
    }

    /**
     * message edit form
     *
     * @param int|null $id
     * @return View
     */
    public function edit(int $id = null): View
    {
        $data = $this->service->get($id);
        return view('page.message.detail')->with($data);
    }

    /**
     * message request store
     *
     * @param MessageRequest $request
     * @return RedirectResponse
     */
    public function store(MessageRequest $request): RedirectResponse
    {
        $rtn = $this->service->store();

        if ($rtn) {
            if ($request->has('isSend')) {
                $this->service->send($rtn);
            }

            if ($request->has('cp')) {
                return redirect()
                    ->route('message.edit', $rtn->id)
                    ->with('success', __('messages.success.create', ['name' => __('words.Message')]));
            } else {
                return redirect()
                    ->route('message.index')
                    ->with('success', __('messages.success.create', ['name' => __('words.Message')]));
            }
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.create', ['name' => __('words.Message')]));
        }
    }

    /**
     * message request update
     *
     * @param MessageRequest $request
     * @param Int $id
     * @return RedirectResponse
     */
    public function update(MessageRequest $request, int $id): RedirectResponse
    {
        $rtn = $this->service->update($id);

        if ($rtn) {
            if ($request->has('isSend')) {
                $this->service->send($rtn);
            }

            return redirect()
                ->route('message.index')
                ->with('success', __('messages.success.update', ['name' => __('words.Message')]));
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.update', ['name' => __('words.Message')]));
        }
    }

    /**
     * message request destroy
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $rtn = $this->service->destroy($id);

        if ($rtn) {
            return redirect()
                ->route('message.index')
                ->with('success', __('messages.success.delete', ['name' => __('words.Message')]));
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.delete', ['name' => __('words.Message')]));
        }
    }

    /**
     * upload images or csv
     *
     * @param Request $request
     * @return JsonResponse []
     */
    public function upload(Request $request): JsonResponse
    {
        $rtn = false;
        $url = '';
        $file = '';
        $fileType = '';
        $uploadType = $request->get('uploadType', '');

        if ($uploadType == 'image') {
            if ($request->has('image')) {
                $fileType = 'image';
                $file = $request->file('image');
            }
        } elseif ($uploadType == 'csv') {
            if ($request->has('csv')) {
                $fileType = 'csv';
                $file = $request->file('csv');
            }
        }

        $rtn = $this->service->upload($file, $fileType);

        if (!empty($rtn)) {
            $url = $rtn;
        }

        return response()->json([
            'success' => !!$rtn,
            'url' => $url
        ]);
    }

    /**
     * upload trumbowyg images
     *
     * @param Request $request
     * @return JsonResponse []
     */
    public function uploadTrumbowygImage(Request $request): JsonResponse
    {
        $rtn = false;
        $link = $this->service->upload($request->image, 'image');

        if (!empty($link)) {
            $rtn = true;
        }

        return response()->json([
            'success' => $rtn,
            'url' => $link
        ]);
    }
}
