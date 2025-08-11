<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 檢查 account 欄位是否存在，如果不存在則先建立
        if (!Schema::hasColumn('users', 'account')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('account')->nullable()->after('email');
            });
        }
        
        // 為現有使用者生成唯一帳號
        $users = \App\Models\User::whereNull('account')->orWhere('account', '')->get();
        foreach ($users as $user) {
            $baseAccount = strtolower(str_replace(['@', '.'], ['_', '_'], explode('@', $user->email)[0]));
            $account = $baseAccount;
            $counter = 1;
            
            while (\App\Models\User::where('account', $account)->where('id', '!=', $user->id)->exists()) {
                $account = $baseAccount . $counter;
                $counter++;
            }
            
            $user->update(['account' => $account]);
        }
        
        // 讓 account 欄位成為必填且唯一
        Schema::table('users', function (Blueprint $table) {
            $table->string('account')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['account']);
            $table->string('account')->nullable()->change();
        });
    }
};
