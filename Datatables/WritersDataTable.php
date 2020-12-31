<?php

namespace Modules\Article\Datatables;

use App\DataTables\BaseDataTable;
use Modules\Article\Entities\Writer;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Role;

class WritersDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
        ->eloquent($query)
        ->addColumn('action', function ($row) {
            $action = '<div class="btn-group dropdown m-r-10">
            <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
            <ul role="menu" class="dropdown-menu pull-right">
            <li><a href="javascript:;" onclick="viewWriter('.$row->id.')"><i class="fa fa-eye" aria-hidden="true"></i> View</a></li>';

            $action .= '<li><a href="'.route('admin.employees.edit', $row->id).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>';

            $action .= '</ul> </div>';

            return $action;
        })
        ->editColumn('name', function ($row) {
            if($row->leaves()->where('leave_dates', 'LIKE', '%'.date('Y-m').'%')->count() > 0) {
                $leave = '<span class="label label-danger">Leaves taken</span>';
            }

            return '<a href="javascript:;" onclick="viewWriter('.$row->id.')">'.ucfirst($row->name).'</a> '. ($leave ?? '');
        })
        ->addColumn('rate', function ($row) {
            return $row->rate['rate'] ?? '--';
        })
        ->addColumn('pending articles', function ($row) {
            return $row->articles->where('writing_status', 0)->count();
        })
        ->addColumn('completed articles', function ($row) {
            return $row->articles->where('writing_status', 2)->count();
        })
        ->addColumn('rating', function ($row) {
            $allRating = $row->articles->where('writing_status', 2)->pluck('rating')->toArray();
            $rating = array_sum($allRating) != 0 ? number_format(array_sum($allRating)/count($allRating), 2) : 0;
            $bars = '';

            if ($rating > 0) {
                for($i=0; $i < number_format($rating); $i++) {
                    $bars .= '<span class="fa fa-star checked"></span>';
                }

                for($i=$i; $i < 5; $i++) {
                    $bars .= '<span class="fa fa-star"></span>';
                }

            } else {
                for($i=0; $i < 5; $i++) {
                    $bars .= '<span class="fa fa-star"></span>';
                }
            }

            return $rating . ' - '. $bars;
        })
        ->editColumn('gender', function ($row) {
            return ucfirst($row->gender);
        })
        ->addColumn('role', function ($row) {
            return ucfirst($row->roles->last()->name);
        })
        ->addColumn('words written', function ($row) {
            $articles = $row->articles->where('writing_status', 2)->pluck('word_count')->toArray();
            return array_sum($articles);
        })
        ->addIndexColumn()
        ->rawColumns(['action', 'name', 'rating']);

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Writer $model)
    {
        $request = $this->request();

        if (auth()->user()->hasRole('Inhouse_writer') || auth()->user()->hasRole('remote_writer')) {
            $writer =  $model->where('id', auth()->id());
        } else {
            $writer =  $model
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->whereHas('roles', function($q){
                return $q->whereIn('name', ['Inhouse_writer', 'Writer_head', 'remote_writer']);
            })
            ->groupBy('users.id');
        }

        return $writer;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
        ->setTableId('writers-table')
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
        ->orderBy(0)
        ->destroy(true)
        ->responsive(true)
        ->serverSide(true)
        ->stateSave(true)
        ->processing(true)
        ->language(__("app.datatable"))
        ->buttons(
            Button::make(['extend'=> 'export','buttons' => ['excel', 'csv']])
        )
        ->parameters([
            'initComplete' => 'function () {
               window.LaravelDataTables["writers-table"].buttons().container()
               .appendTo( ".bg-title .text-right")
           }',
           'fnDrawCallback' => 'function( oSettings ) {
            $("body").tooltip({
                selector: \'[data-toggle="tooltip"]\'
                })
            }',
        ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            '#' => ['data' => 'id', 'name' => 'id', 'visible' => true],
            'Name' => ['data' => 'name', 'name' => 'name'],
            'role' => ['data' => 'role', 'name' => 'role_user.role_id'],
            'rate',
            'pending articles',
            'completed articles',
            'words written',
            'gender' => ['data' => 'gender', 'name' => 'gender'],
            'rating',
            Column::computed('action')
            ->exportable(false)
            ->printable(false)
            ->orderable(true)
            ->searchable(false)
            ->width(150)
            ->addClass('text-center')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Writers_' . date('YmdHis');
    }

    public function pdf()
    {
        set_time_limit(0);
        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }
}
