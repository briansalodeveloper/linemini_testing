<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ContentPlanRequest;
use App\Services\ContentPlanService;
use App\Models\ContentPlan;

class NoticeController extends Controller
{
    /**
     * @param ContentPlanService $service
     * @return void
     */
    public function __construct(ContentPlanService $service)
    {
        $this->service = $service;
    }

    /**
     * notice list
     *
     * @return View
     */
    public function index(): View
    {
        $data = $this->service->all(ContentPlan::CONTENTTYPE_NOTICE);
        return view('page.notice.index')->with($data);
    }

    /**
     * notice create form
     * @return View
     */
    public function create(): View
    {
        return $this->edit();
    }

    /**
     * notice edit form
     *
     * @param int|null $id
     * @return View
     */
    public function edit(int $id = null): View
    {
        $data = $this->service->get($id);
        return view('page.notice.detail')->with($data);
    }

    /**
     * notice request store
     *
     * @param ContentPlanRequest $request
     * @return RedirectResponse
     */
    public function store(ContentPlanRequest $request): RedirectResponse
    {
        $rtn = $this->service->store(ContentPlan::CONTENTTYPE_NOTICE);

        if ($rtn) {
            if ($request->has('cp')) {
                return redirect()
                    ->route('notice.edit', $rtn->id)
                    ->with('success', __('messages.success.create', ['name' => __('words.Deals')]));
            } else {
                return redirect()
                    ->route('notice.index')
                    ->with('success', __('messages.success.create', ['name' => __('words.Deals')]));
            }
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.create', ['name' => __('words.Deals')]));
        }
    }

    /**
     * notice request update
     *
     * @param ContentPlanRequest $request
     * @param Int $id
     * @return RedirectResponse
     */
    public function update(ContentPlanRequest $request, int $id): RedirectResponse
    {
        $rtn = $this->service->update($id, ContentPlan::CONTENTTYPE_NOTICE);

        if ($rtn) {
            return redirect()
                ->route('notice.index')
                ->with('success', __('messages.success.update', ['name' => __('words.Deals')]));
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.update', ['name' => __('words.Deals')]));
        }
    }

    /**
     * notice request destroy
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $rtn = $this->service->destroy($id);

        if ($rtn) {
            return redirect()
                ->route('notice.index')
                ->with('success', __('messages.success.delete', ['name' => __('words.Deals')]));
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.delete', ['name' => __('words.Deals')]));
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

        if ($uploadType == 'thumbnail') {
            if ($request->has('thumbnail')) {
                $fileType = 'image';
                $file = $request->file('thumbnail');
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
