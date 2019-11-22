<?php

namespace App\Http\Controllers\Api;

use App\Candidate;
use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PDF;
use Excel;
use Validator;

class CandidateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        /*this will be always on the latest*/
        if ($request->filter == 'paid') {
            $result = Candidate::whereNotNull(['amount', 'currency'])->get();

        } elseif ($request->filter == 'unpaid') {
            $result = Candidate::whereNull(['amount', 'currency'])->get();

        } else {
            $result = Candidate::all();

        }

        if (isset($request->search)) {
            $result = full_text_search($result, $request->search, []);
        }

        return CandidateResource::collection(paginate($result, $request->itemsPerPage, $request->page));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return UserResource
     */
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'Name' => 'required|min:3',
            'Email_Address' => 'required|email|unique:candidates,Email_Address,' . $id,
            'Scholarship' => 'required',
            'status' => 'required'
        ]);


        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);

        }


        $candidate = Candidate::findOrFail($id);
        $candidate->fill($request->all())->save();

        $return = ["status" => "Success",
            "error" => [
                "code" => 201,
                "errors" => 'Updated'
            ]];
        return response()->json($return, 201);
    }

    public function receipt($id)
    {
        $candidate = Candidate::findOrFail($id);

        if (isset($candidate->currency) || isset($candidate->amount) || isset($candidate->Name) || isset($candidate->Scholarship)) {
            $pdf = PDF::loadView('pdf.receipt', compact('candidate'));
            return $pdf->download($candidate->Name . '_receipt.pdf');
        } else {
            return "Please Write effective code for validation";
        }

    }

    public function confirmation($id)
    {
        $candidate = Candidate::findOrFail($id);

        if (isset($candidate->currency) || isset($candidate->amount) || isset($candidate->Name) || isset($candidate->Scholarship)) {
            $pdf = PDF::loadView('pdf.confirmation', compact('candidate'));
            return $pdf->download($candidate->Name . '_confirmation.pdf');
        } else {
            return "Please Write effective code for validation";
        }

    }

    public function send($id)
    {
        $candidate = Candidate::findOrFail($id);

        if (isset($candidate->currency) && isset($candidate->amount) && isset($candidate->amount) && isset($candidate->Name) && isset($candidate->Scholarship)) {
            $pdf_confirmation = PDF::loadView('pdf.confirmation', compact('candidate'));
            $pdf_receipt = PDF::loadView('pdf.receipt', compact('candidate'));

            $data = array('name' => $candidate->Name, "body" => "Please ,  find the attached official registration letter and receipt .");
            /*Email_Address*/
            Mail::send('emails.mail', $data, function ($message) use ($candidate, $pdf_confirmation, $pdf_receipt) {
                $message->to($candidate->Email_Address, $candidate->Name)
                    ->subject('Receipt and Confirmation Letter')
                    ->attachData($pdf_confirmation->output(), $candidate->Name . '_confirmation.pdf')
                    ->attachData($pdf_receipt->output(), $candidate->Name . '_receipt.pdf');

            });
            if (Mail::failures()) {
                $return = ["status" => "Error",
                    "error" => [
                        "code" => 406,
                        "errors" => 'Unable to send Mail'
                    ]];
                return response()->json($return, 406);
            } else {
                $candidate->update(['status' => 'email_send']);

                $return = ["status" => "Success",
                    "error" => [
                        "code" => 200,
                        "errors" => 'Email Send'
                    ]];
                return response()->json($return, 200);
            }

        } else {
            $return = ["status" => "Error",
                "error" => [
                    "code" => 405,
                    "errors" => 'Invalid Data'
                ]];
            return response()->json($return, 405);
        }

    }



    public function csvLoad(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'import_file' => 'required'
        ]);


        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);

        }


        DB::table('candidates')->truncate();

        Excel::import(new Candidate(), request()->file('import_file'));


        $return = ["status" => "Success",
            "success" => [
                "code" => 201,
                "message" => 'Data import Successfully'
            ]];
        return response()->json($return, 201);
    }
}
