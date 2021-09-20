<?php

namespace Modules\Article\Datatables;

use App\DataTables\BaseDataTable;
use Modules\Article\Entities\Invoice;
use Modules\Article\Entities\ArticleSetting;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class PayslipsDataTable extends BaseDataTable
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
            $action = '--';
            return $action;
        })
        ->editColumn('name', function ($row) {
            return '<a href="javascript:;" onclick="viewInvoice('.$row->id.')">'.$row->name.'</a>';
        })
        ->editColumn('paid_to', function ($row) {
            return $row->paid_to;
        })
        ->editColumn('amount', function ($row) {
            return $row->amount;
        })
        ->editColumn('status', function ($row) {
            return $row->status ? 'Paid' : 'Unpaid';
        })
        ->addIndexColumn()
        ->rawColumns(['action']);

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Invoice $model)
    {
        $writerRole = ArticleSetting::where('type', 'writer')->first()->value;
        $request = $this->request();

        if (auth()->user()->hasRole($writerRole)) {
            $model = $model->where('paid_to', auth()->id());
        }

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
        ->setTableId('payslips-table')
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
               window.LaravelDataTables["payslips-table"].buttons().container()
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
            'name' => ['data' => 'name', 'name' => 'name'],
            'payment_for' => ['data' => 'paid_to', 'name' => 'paid_to'],
            'amount' => ['data' => 'amount', 'name' => 'amount'],
            'status' => ['data' => 'status', 'name' => 'status'],
            'created_at' => ['data' => 'created_at', 'name' => 'created_at'],
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
        return 'Payslips_' . date('YmdHis');
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
