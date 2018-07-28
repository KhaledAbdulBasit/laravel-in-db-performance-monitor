<?php

namespace ASamir\InDbPerformanceMonitor;

use Illuminate\Database\Eloquent\Model;

class LogErrors extends Model {

    protected $connection = 'inDbMonitorConn';
    protected $table = 'log_errors';
    protected $primaryKey = 'id';
    protected $fillable = [
        'message', 'code', 'file', 'line', 'trace', 'request_id'
    ];

    public static function inDbLogError(\Exception $exception) {
        if (!request('__asamir_request_id'))
            return;
        // Save Errors Log
        LogErrors::create([
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'request_id' => request('__asamir_request_id'),
        ]);

        // Update request data
        LogRequests::find(request('__asamir_request_id'))->update([
            'has_errors' => 1,
        ]);
    }

    public function request() {
        return $this->belongsTo('LogRequests', 'request_id');
    }

}