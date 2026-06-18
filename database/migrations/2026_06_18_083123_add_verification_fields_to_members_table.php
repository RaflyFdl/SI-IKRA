public function up()
{
    Schema::table('members', function (Blueprint $table) {
        $table->string('verification_token', 255)->nullable()->after('status');
        $table->timestamp('email_verified_at')->nullable()->after('verification_token');
    });
}

public function down()
{
    Schema::table('members', function (Blueprint $table) {
        $table->dropColumn(['verification_token', 'email_verified_at']);
    });
}