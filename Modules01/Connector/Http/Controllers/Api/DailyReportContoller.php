<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\User;
use App\Business;
use App\Transaction;
use Modules\Crm\Entities\CustomerAudit;
use App\Contact;
use Modules\Essentials\Entities\EssentialsAttendance;
use Modules\SocialManagement\Entities\Social;
use Modules\SocialManagement\Entities\SocialAudit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DailyReportContoller extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $report_date = $request->input('report_date', Carbon::now()->format('Y-m-d'));

        $business_location = Business::findOrFail($business_id);

        $UserCount = User::where('business_id', $business_id)
            ->where('user_type', 'user')
            ->count();

        $sell = Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->whereDate('transaction_date', $report_date)
            ->count();

        $totalAmount = number_format((double) Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $expense = number_format((double) DB::table('transactions')
            ->where('type', 'expense')
            ->where('business_id', $business_id)
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $bill = Transaction::where('type', 'expense')
            ->where('business_id', $business_id)
            ->whereDate('transaction_date', $report_date)
            ->count();

        $paymentStatusData = Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->whereDate('transaction_date', $report_date)
            ->count();

        $totalpaymentStatusData = number_format((double) Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $due = Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('payment_status', 'due')
            ->whereDate('transaction_date', $report_date)
            ->count();

        $totaldue = number_format((double) Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('payment_status', 'due')
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $paid = Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('payment_status', 'paid')
            ->whereDate('transaction_date', $report_date)
            ->count();

        $totalpaid = number_format((double) Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('payment_status', 'paid')
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $partial = Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('payment_status', 'partial')
            ->whereDate('transaction_date', $report_date)
            ->count();

        $totalpartial = number_format((double) Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('payment_status', 'partial')
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $paymentMethod = Transaction::where('type', 'sell')
            ->leftJoin('transaction_payments as tp', 'tp.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.transaction_date', $report_date)
            ->whereNotNull('tp.method')
            ->select('tp.method', DB::raw('count(*) as method_count'), DB::raw('sum(tp.amount) as total_amount'))
            ->groupBy('tp.method')
            ->orderBy('total_amount', 'asc')
            ->get();

        $cash = Transaction::where('type', 'sell')
            ->leftJoin('transaction_payments as tp', 'tp.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.transaction_date', $report_date)
            ->where('tp.method', 'cash')
            ->where('tp.business_id', $business_id)
            ->sum('tp.amount');

        $bank_transfer = Transaction::where('type', 'sell')
            ->leftJoin('transaction_payments as tp', 'tp.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.transaction_date', $report_date)
            ->where('tp.method', 'bank_transfer')
            ->where('tp.business_id', $business_id)
            ->sum('tp.amount');

        $advance = Transaction::where('type', 'sell')
            ->leftJoin('transaction_payments as tp', 'tp.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.transaction_date', $report_date)
            ->where('tp.method', 'advance')
            ->where('tp.business_id', $business_id)
            ->sum('tp.amount');

        $totalAmountPayment = Transaction::where('type', 'sell')
            ->leftJoin('transaction_payments as tp', 'tp.transaction_id', '=', 'transactions.id')
            ->whereNotNull('tp.method')
            ->where('transactions.business_id', $business_id)
            ->whereDate('transactions.transaction_date', $report_date)
            ->sum('tp.amount');

        $attendanceCount = EssentialsAttendance::whereDate('created_at', $report_date)
            ->where('business_id', $business_id)
            ->count();

        $purchase = number_format((double) Transaction::where('type', 'purchase')
            ->where('business_id', $business_id)
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $invoice = Transaction::where('type', 'purchase')
            ->where('business_id', $business_id)
            ->whereDate('transaction_date', $report_date)
            ->count();

        $shipment = Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->whereDate('transaction_date', $report_date)
            ->count();

        $ordered = number_format((double) Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('shipping_status', 'ordered')
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $packed = number_format((double) Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('shipping_status', 'packed')
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $shipped = number_format((double) Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('shipping_status', 'shipped')
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $delivered = number_format((double) Transaction::where('type', 'sell')
            ->where('business_id', $business_id)
            ->where('shipping_status', 'delivered')
            ->whereDate('transaction_date', $report_date)
            ->sum('final_total'), 4, '.', '');

        $total_staff_follow_up = CustomerAudit::where('business_id', $business_id)
            ->whereDate('created_at', $report_date)
            ->count();

        $total_staff = Contact::where("contacts.business_id", $business_id)
            ->leftJoin('user_contact_access as uca', 'uca.contact_id', '=', 'contacts.id')
            ->whereNotNull('uca.user_id')
            ->distinct('uca.user_id')
            ->count('uca.user_id');

        $buy = CustomerAudit::where('business_id', $business_id)
            ->whereDate('created_at', $report_date)
            ->where('customer_buy', 1)
            ->count();

        $pickup_call = CustomerAudit::where('business_id', $business_id)
            ->whereDate('created_at', $report_date)
            ->where('customer_call', 1)
            ->count();

        $total_social = Social::where('business_id', $business_id)->count();

        $TotalPostMorning = SocialAudit::where('business_id', $business_id)
            ->whereDate('created_at', $report_date)
            ->where('posted_morning', 1)
            ->count();

        $TotalPostAfternoon = SocialAudit::where('business_id', $business_id)
            ->whereDate('created_at', $report_date)
            ->where('posted_afternoon', 1)
            ->count();

        //user that assign for the social
        $total_user = DB::table('social_audits')
            ->select(DB::raw('COUNT(DISTINCT social_audits.user_id) AS total_user'))
            ->leftJoin('users as user', 'user.id', '=', 'social_audits.user_id')
            ->where('social_audits.business_id', $business_id)
            ->first();

        $FacebookPostMorning = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'ហ្វេសប៊ុក')
            ->where('posted_morning', 1)
            ->count();

        $Facebook_pagePostMorning = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'ផេក')
            ->where('posted_morning', 1)
            ->count();

        $YoutubePostMorning = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'YouTube')
            ->where('posted_morning', 1)
            ->count();

        $TicktokPostMorning = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'Ticktok')
            ->where('posted_morning', 1)
            ->count();

        $Telegram_channelPostMorning = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'Telegram')
            ->where('posted_morning', 1)
            ->count();

        $InstagramPostMorning = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'Instagram')
            ->where('posted_morning', 1)
            ->count();

        $FacebookPostAfternoon = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'ហ្វេសប៊ុក')
            ->where('posted_afternoon', 1)
            ->count();

        $Facebook_pagePostAfternoon = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'ផេក')
            ->where('posted_afternoon', 1)
            ->count();

        $YoutubePostAfternoon = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'YouTube')
            ->where('posted_afternoon', 1)
            ->count();

        $TicktokPostAfternoon = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'Ticktok')
            ->where('posted_afternoon', 1)
            ->count();

        $Telegram_channelPostAfternoon = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'Telegram')
            ->where('posted_afternoon', 1)
            ->count();

        $InstagramPostAfternoon = SocialAudit::where('social_audits.business_id', $business_id)
            ->leftJoin('social_table as st', 'st.id', '=', 'social_audits.social_id')
            ->leftJoin('social_categories as sc', 'sc.id', '=', 'st.social_category_id')
            ->whereDate('social_audits.created_at', $report_date)
            ->where('sc.name', 'Instagram')
            ->where('posted_afternoon', 1)
            ->count();

        $FacebookCount = Social::leftJoin('social_categories', 'social_categories.id', '=', 'social_table.social_category_id')
            ->where('social_table.business_id', $business_id)
            ->where('social_categories.name', 'ហ្វេសប៊ុក')
            ->count();

        $Facebook_pageCount = Social::where('social_table.business_id', $business_id)
            ->leftJoin('social_categories', 'social_categories.id', '=', 'social_table.social_category_id')
            ->where('social_categories.name', 'ផេក')
            ->count();

        $YouTubeCount = Social::where('social_table.business_id', $business_id)
            ->leftJoin('social_categories', 'social_categories.id', '=', 'social_table.social_category_id')
            ->where('social_categories.name', 'YouTube')
            ->count();

        $TicktokCount = Social::where('social_table.business_id', $business_id)
            ->leftJoin('social_categories', 'social_categories.id', '=', 'social_table.social_category_id')
            ->where('social_categories.name', 'Ticktok')
            ->count();

        $InstagramCount = Social::where('social_table.business_id', $business_id)
            ->leftJoin('social_categories', 'social_categories.id', '=', 'social_table.social_category_id')
            ->where('social_categories.name', 'Instagram')
            ->count();

        $TelegramCount = Social::where('social_table.business_id', $business_id)
            ->leftJoin('social_categories', 'social_categories.id', '=', 'social_table.social_category_id')
            ->where('social_categories.name', 'Telegram')
            ->count();

        $consignment = number_format((double) Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
            ->join('business_locations AS bl', 'transactions.location_id', '=', 'bl.id')
            ->leftJoin('transaction_sell_lines as tsl', function ($join) {
                $join->on('transactions.id', '=', 'tsl.transaction_id')
                    ->whereNull('tsl.parent_sell_line_id');
            })
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'draft')
            ->where('transactions.sub_status', 'quotation')
            ->whereDate('transactions.transaction_date', $report_date)
            ->groupBy('transactions.id')
            ->sum('final_total'), 4, '.', '');

        $totalconsignment = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
            ->join('business_locations AS bl', 'transactions.location_id', '=', 'bl.id')
            ->leftJoin('transaction_sell_lines as tsl', function ($join) {
                $join->on('transactions.id', '=', 'tsl.transaction_id')
                    ->whereNull('tsl.parent_sell_line_id');
            })
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'draft')
            ->where('transactions.sub_status', 'quotation')
            ->whereDate('transactions.transaction_date', $report_date)
            ->groupBy('transactions.id')
            ->count();

        return response()->json([
            'UserCount' => $UserCount,
            'attendanceCount' => $attendanceCount,
            'report_date' => $report_date,
            'sell' => $sell,
            'expense' => $expense,
            'paymentStatusData' => $paymentStatusData,
            'totalpaymentStatusData' => $totalpaymentStatusData,
            'paymentMethod' => $paymentMethod,
            'totalAmount' => $totalAmount,
            'totalAmountPayment' => $totalAmountPayment,
            'due' => $due,
            'totaldue' => $totaldue,
            'paid' => $paid,
            'totalpaid' => $totalpaid,
            'partial' => $partial,
            'totalpartial' => $totalpartial,
            'cash' => $cash,
            'bank_transfer' => $bank_transfer,
            'advance' => $advance,
            'bill' => $bill,
            'purchase' => $purchase,
            'invoice' => $invoice,
            'delivered' => $delivered,
            'shipped' => $shipped,
            'packed' => $packed,
            'ordered' => $ordered,
            'shipment' => $shipment,
            'total_staff_follow_up' => $total_staff_follow_up,
            'total_staff' => $total_staff,
            'buy' => $buy,
            'pickup_call' => $pickup_call,
            'FacebookPostMorning' => $FacebookPostMorning,
            'Facebook_pagePostMorning' => $Facebook_pagePostMorning,
            'YoutubePostMorning' => $YoutubePostMorning,
            'TicktokPostMorning' => $TicktokPostMorning,
            'Telegram_channelPostMorning' => $Telegram_channelPostMorning,
            'InstagramPostMorning' => $InstagramPostMorning,
            'FacebookPostAfternoon' => $FacebookPostAfternoon,
            'Facebook_pagePostAfternoon' => $Facebook_pagePostAfternoon,
            'YoutubePostAfternoon' => $YoutubePostAfternoon,
            'TicktokPostAfternoon' => $TicktokPostAfternoon,
            'Telegram_channelPostAfternoon' => $Telegram_channelPostAfternoon,
            'InstagramPostAfternoon' => $InstagramPostAfternoon,
            'consignment' => $consignment,
            'totalconsignment' => $totalconsignment,
            'total_social' => $total_social,
            'business_location' => $business_location->name,
            'total_user' => $total_user->total_user,
            'TotalPostMorning' => $TotalPostMorning,
            'TotalPostAfternoon' => $TotalPostAfternoon,
            'FacebookCount' => $FacebookCount,
            'YouTubeCount' => $YouTubeCount,
            'Facebook_pageCount' => $Facebook_pageCount,
            'InstagramCount' => $InstagramCount,
            'TicktokCount' => $TicktokCount,
            'TelegramCount' => $TelegramCount,
        ]);
    }
}
