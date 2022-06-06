<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use App\Models\ContentPlan;
use Illuminate\Http\UploadedFile;

class NoticeControllerTest extends TestCase
{
    /**
     * Only admin user can access the notice controller
     *
     * @return RedirectResponse
     */
    public function testOnlyAdminUserCanAccessTheNoticeController()
    {
        $responseNotAuthenticated = $this->get('/notice')
                                    ->assertRedirect('/');

        $responseAuthenticated = $this->actingAs($this->admin())
                                ->get('/notice')
                                ->assertStatus(200);
    }
    /**
     * accessing the notice.index route
     *
     * @return ReturnView
     */
    public function testIndexFunctionReturnView()
    {
        $response = $this->actingAs($this->admin())
                  ->get('/notice')
                  ->assertViewIs('page.notice.index');
    }
    /**
     * accessing the notice.edit and notice.create route
     *
     * @return ReturnView
     */
    public function testCreateAndEditFunctionReturnView()
    {
        $response = $this->actingAs($this->admin())
                    ->get('/notice/create')
                    ->assertViewIs('page.notice.detail');
    }

    /**
     * accessing the store route with sample form and checking the validation errors
     *
     * @return RedirectResponse
     */
    public function testStoreFunctionReturnRedirectResponse()
    {
        $response = $this->actingAs($this->admin())
                    ->post('/notice/store', $this->formData())
                    ->assertSessionDoesntHaveErrors($this->required())
                    ->assertRedirect('/notice');

        //when the submitted form has cp
        $latestId = ContentPlan::orderBy('contentPlanId', 'desc')->first()->id;
        $responseWithCp = $this->actingAs($this->admin())
                          ->post('/notice/store', $this->formDataHasCp())
                          ->assertRedirect('/notice/' . ($latestId + 1) . '/edit');
    }

    /**
     * accessing the update route and update content plan with sample form and checking the validation errors
     *
     * @return RedirectResponse
     */
    public function testUpdateFunctionReturnRedirectResponse()
    {
        $update = ContentPlan::first();
        $response = $this->actingAs($this->admin())
                    ->post('/notice/' . $update->id . '/update', $this->formData())
                    ->assertSessionDoesntHaveErrors($this->required())
                    ->assertRedirect('/notice');
    }

    /**
     * accessing the destroy route and delete some data in content plan
     *
     * @return RedirectResponse
     */
    public function testDestroyFunctionReturnRedirectResponse()
    {
        $delete = ContentPlan::first();
        $response = $this->actingAs($this->admin())
                    ->post('/notice/' . $delete->id . '/destroy')
                    ->assertRedirect('/notice');
    }

    /**
     * accessing the upload route and upload with sample image or csv file
     *
     * @return JsonResponse
     */
    public function testUploadFunctionReturnJsonResponse()
    {
         //for image upload
         $responseThumbnail = $this->actingAs($this->admin())
                            ->postJson('/notice/upload', $this->uploadThumbnail())
                            ->assertStatus(200)
                            ->assertJsonCount(2);

         //for csv upload
         $responseCsv = $this->actingAs($this->admin())
                        ->postJson('/notice/upload', $this->uploadCsv())
                        ->assertStatus(200)
                        ->assertJsonCount(2);
    }

    /**
     * accessing the uploadTrumbowygImage route and upload sample image
     *
     * @return JsonResponse
     */
    public function testUploadTrumbowygImageFunctionReturnJsonResponse()
    {
        $responseImage = $this->actingAs($this->admin())
                         ->postJson('/notice/uploadTrumbowygImage', $this->uploadImage())
                         ->assertStatus(200)
                         ->assertJsonCount(2);
    }


    /*======================================================================
     * PRIVATES
     *======================================================================*/

    private function admin()
    {
        return Admin::find(1);
    }

    private function uploadImage()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        return ['image' => $file];
    }

    private function uploadThumbnail()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        return [
          'uploadType' => 'thumbnail',
          'thumbnail' => $file,
        ];
    }

    private function uploadCsv()
    {
        $file = UploadedFile::fake()->create('avatar.csv');
        return [
          'uploadType' => 'csv',
          'csv' => $file,
        ];
    }

    private function required()
    {
        $formData = $this->formData();
        $selectPublicationDateTime = $formData['selectPublicationDateTime'];
        $displayTargetFlg = $formData['displayTargetFlg'];
        $contentPlanId = $formData['contentPlanId'];
        $displayTarget = $formData['displayTarget'];

        $rtn = ['openingLetter','contentTypeNews','displayTargetFlg','openingImg','targetCode'];

        if (!is_null($selectPublicationDateTime) && $selectPublicationDateTime == 1) {
             array_push($rtn, 'startDateTime', 'endDateTime');
        }

        if ($displayTargetFlg == ContentPlan::DSPTARGET_UNIONMEMBER && ($contentPlanId || (!$contentPlanId && empty($displayTarget)))) {
             array_push($rtn, 'unionMemberCsv');
        }

        if ($displayTargetFlg == ContentPlan::DSPTARGET_UB) {
            array_push($rtn, 'utilizationBusiness');
        }

        if ($displayTargetFlg == ContentPlan::DSPTARGET_AO) {
            array_push($rtn, 'affiliationOffice');
        }
        
        return $rtn;
    }
    
    private function formData()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
          return [
            'targetCode' => '3',
            'displayTarget' => '',
            'contentPlanId' => '',
            'uploadType' => 'thumbnail unit',
            'openingLetter' => 'title unit testing',
            'selectPublicationDateTime' => '2',
            'startDate' => '2022-04-21',
            'endDate' => '2022-04-25',
            'contentTypeNews' => '1',
            'displayTargetFlg' => '2',
            'unionMemberCsv' => 'null',
            'utilizationBusiness' => '2',
            'openingImg' => 'http://localhost/storage/tmp/2022-04-25/1650866797/images/signature.png',
            'contents' => '<p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">this is body12 content</font></font></p>',
            'thumbnail' => $file,
          ];
    }

    private function formDataHasCp()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
          return [
            'uploadType' => 'thumbnail unit',
            'openingLetter' => 'title unit testing',
            'selectPublicationDateTime' => '0',
            'startDate' => '04/25/2022',
            'startTime' => '02:06 PM',
            'endDate' => '04/26/2022',
            'endTime' => '2:06 PM',
            'contentTypeNews' => '1',
            'displayTargetFlg' => '2',
            'unionMemberCsv' => 'null',
            'utilizationBusiness' => '2',
            'openingImg' => 'http://localhost/storage/tmp/2022-04-25/1650866797/images/signature.png',
            'contents' => '<p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">this is body content</font></font></p>',
            'startDateTime' => '04/25/2022 02:06 PM',
            'endDateTime' => '04/26/2022 2:06 PM',
            '//notice/store' => 'null',
            'thumbnail' => $file,
            'cp' => '1',
          ];
    }
}
