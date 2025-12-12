<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\Admin\AdminActions;

class AdminController extends BaseController
{
    public function index()
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/login');
        }

        $roleFilter = $this->request->getGet('role') ?? 'all';
        $statusFilter = $this->request->getGet('status') ?? 'all';

        $users = AdminActions::getUsers($roleFilter, $statusFilter);

        return view('admin/administration', [
            'roleFilter' => $roleFilter,
            'statusFilter' => $statusFilter,
            'users' => $users,
            'msg' => session()->getFlashdata('msg') ?? '',
        ]);
    }

    public function action()
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/login');
        }

        $action = $this->request->getPost('action');

        $result = AdminActions::handle($action, $this->request);

        return redirect()->to('/admin')
            ->with('msg', $result['message'] ?? 'Done');
    }

    public function searchReport()
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/login');
        }

        $from = $this->request->getPost('from') ?? '';
        $to = $this->request->getPost('to') ?? '';

        $rows = [];
        $error = '';

        if ($from && $to) {
            $fromDT = $from . ' 00:00:00';
            $toDT = $to . ' 23:59:59';

            $db = \Config\Database::connect();

            $rows = $db->table('search_logs sl')
                ->select("
                sl.created_at AS fecha,
                CONCAT(u.first_name,' ',u.last_name) AS usuario,
                sl.origin AS salida,
                sl.destination AS llegada,
                sl.results_count AS resultados
            ", false)
                ->join('users u', 'u.id = sl.user_id', 'left')
                ->where('sl.created_at >=', $fromDT)
                ->where('sl.created_at <=', $toDT)
                ->orderBy('sl.created_at', 'DESC')
                ->get()
                ->getResultArray();
        } elseif ($from || $to) {
            $error = 'Debes seleccionar ambas fechas.';
        }

        return view('admin/report_searches', [
            'from' => $from,
            'to' => $to,
            'rows' => $rows,
            'error' => $error,
        ]);
    }

}
