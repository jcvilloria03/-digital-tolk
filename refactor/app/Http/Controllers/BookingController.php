<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;
use Illuminate\Support\Arr;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|int|unique:user'
        ]);
        
        $response = null;
        $userId = $request->get('user_id');
        $adminRoleId = env('ADMIN_ROLE_ID');
        $superadminRoleId = env('SUPERADMIN_ROLE_ID');

        if($userId) {
            return response($this->repository->getUsersJobs($userId));
        }
        elseif($request->__authenticatedUser->user_type == $adminRoleId || $request->__authenticatedUser->user_type == $superadminRoleId) {
            return response($this->repository->getAll($request));
        }
        else {
            return response(['error' => 'Resource not found.'], 404);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $job = $this->repository->with('translatorJobRel.user')->findOrFail($id);
            return response($job);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Resource not found.'], 404);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'immediate' => 'required|string',
        ]);

        $data = $request->all();

        $response = $this->repository->store($request->__authenticatedUser, $data);

        return response($response);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'due' => 'required|boolean',
            'admin_comments' => 'required|string',
            'reference' => 'required|string',
        ]);

        $data = $request->all();
        $user = $request->__authenticatedUser;
        $response = $this->repository->updateJob($id, Arr::except($data, ['_token', 'submit']), $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {   
        $this->validate($request, [
            'user_type' => 'required|string',
            'user_email_job_id' => 'required|unique:job',
            'user_email' => 'required|email',
        ]);

        $adminSenderEmail = config('app.adminemail');
        $data = $request->all();

        $response = $this->repository->storeJobEmail($data);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|int',
            'page' => 'sometimes|int',
        ]);

        $userId = $request->get('user_id');
        $page = $request->get('page') ?? "1";

        if($userId) {
            $response = $this->repository->getUsersJobsHistory($userId, $page);
            return response($response);
        }

        return response(['error' => 'user_id not found'], 400);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required|int',
        ]);

        $data = $request->input('job_id');
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJob($data, $user);

        return response($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required|int',
        ]);

        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJobWithId($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required|int',
        ]);

        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        $response = $this->repository->cancelJobAjax($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required|int',
            'user_id' => 'required|int|unique:user',
        ]);

        $data = $request->only(['job_id', 'user_id']);

        $response = $this->repository->endJob($data);

        return response($response);

    }

    public function customerNotCall(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required|int',
        ]);

        $data = $request->get('job_id');

        $response = $this->repository->customerNotCall($data);

        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $user = $request->__authenticatedUser;

        $response = $this->repository->getPotentialJobs($user);

        return response($response);
    }

    public function distanceFeed(Request $request)
    {
        $this->validate($request, [
            'flagged' => 'required|string',
            'manually_handled' => 'required|string',
            ''
        ]);

        $data = $request->all();

        $distance = $data['distance'] ?? "";
        $time = $data['time'] ?? "";
        $jobid = $data['jobid'] ?? "";
        $session = $data['session_time'] ?? "";
        
        $flagged = $data['flagged'] == 'true' ? 'yes' : 'no';
        $manually_handled = $data['manually_handled'] == 'true' ? 'yes' : 'no';
        $by_admin = $data['by_admin'] == 'true' ? 'yes' : 'no';
        
        $admincomment = $data['admincomment'] ?? "";

        if ($time || $distance) {
            $affectedRows = Distance::where('job_id', '=', $jobid)
                ->update([
                    'distance' => $distance,
                    'time' => $time
                ]);
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            $affectedRows1 = Job::where('id', '=', $jobid)
                ->update([
                    'admin_comments' => $admincomment,
                    'flagged' => $flagged,
                    'session_time' => $session,
                    'manually_handled' => $manually_handled,
                    'by_admin' => $by_admin
                ]);

        }

        return response('Record updated!');
    }

    public function reopen(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required|int',
            'user_id' => 'required|int|unique:user',
        ]);

        $data = $request->only(['job_id', 'user_id']);
        $response = $this->repository->reopen($data);

        return response($response);
    }

    public function resendNotifications(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required|int',
        ]);
        $jobId = $request->get('job_id');
        $job = $this->repository->find($jobId);
        if (!$job) {
            return response(['error' => 'Job not found'], 404);
        }
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required|int',
        ]);

        $jobId = $request->get('job_id');
        $job = $this->repository->find($jobId);
        if (!$job) {
            return response(['error' => 'Job not found'], 404);
        }

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }
}
