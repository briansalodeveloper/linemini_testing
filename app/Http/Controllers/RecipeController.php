<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ContentPlanRequest;
use App\Services\ContentPlanService;
use App\Models\ContentPlan;

class RecipeController extends Controller
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
     * recipe list
     *
     * @return View
     */
    public function index(): View
    {
        $data = $this->service->all(ContentPlan::CONTENTTYPE_RECIPE);
        return view('page.recipe.index')->with($data);
    }

    /**
     * recipe create form
     * @return View
     */
    public function create(): View
    {
        return $this->edit();
    }

    /**
     * recipe edit form
     *
     * @param int|null $id
     * @return View
     */
    public function edit(int $id = null): View
    {
        $data = $this->service->get($id);
        return view('page.recipe.detail')->with($data);
    }

    /**
     * recipe request store
     *
     * @param ContentPlanRequest $request
     * @return RedirectResponse
     */
    public function store(ContentPlanRequest $request): RedirectResponse
    {
        $rtn = $this->service->store(ContentPlan::CONTENTTYPE_RECIPE);

        if ($rtn) {
            if ($request->has('cp')) {
                return redirect()
                    ->route('recipe.edit', $rtn->id)
                    ->with('success', __('messages.success.create', ['name' => __('words.Recipe')]));
            } else {
                return redirect()
                    ->route('recipe.index')
                    ->with('success', __('messages.success.create', ['name' => __('words.Recipe')]));
            }
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.create', ['name' => __('words.Recipe')]));
        }
    }

    /**
     * recipe request update
     *
     * @param ContentPlanRequest $request
     * @param Int $id
     * @return RedirectResponse
     */
    public function update(ContentPlanRequest $request, int $id): RedirectResponse
    {
        $rtn = $this->service->update($id, ContentPlan::CONTENTTYPE_RECIPE);

        if ($rtn) {
            return redirect()
                ->route('recipe.index')
                ->with('success', __('messages.success.update', ['name' => __('words.Recipe')]));
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.update', ['name' => __('words.Recipe')]));
        }
    }

    /**
     * recipe request destroy
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $rtn = $this->service->destroy($id);

        if ($rtn) {
            return redirect()
                ->route('recipe.index')
                ->with('success', __('messages.success.delete', ['name' => __('words.Recipe')]));
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.failed.delete', ['name' => __('words.Recipe')]));
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
