<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🏖️ Creating Leave Request Seeder - 7 Records Only...\n";
        echo "═══════════════════════════════════════════════════════\n\n";

        // Pastikan ada users
        $users = User::all();
        if ($users->isEmpty()) {
            echo "❌ No users found! Please run UserSeeder first.\n";

            return;
        }

        echo "👥 Found {$users->count()} users in the system\n\n";

        // Hapus data lama jika ada
        LeaveRequest::truncate();
        echo "🗑️ Old leave request data cleared\n\n";

        $leaveTypeNames = [
            'Cuti Tahunan',
            'Cuti Sakit',
            'Cuti Ibadah',
            'Cuti Keluarga',
        ];

        $existingTypes = LeaveType::whereIn('name', $leaveTypeNames)->get()->keyBy('name');
        $leaveTypes = [];
        foreach ($leaveTypeNames as $name) {
            $leaveTypes[$name] = optional($existingTypes->get($name))?->id ?? LeaveType::create([
                'name' => $name,
                'max_days_per_year' => match ($name) {
                    'Cuti Tahunan' => 12,
                    'Cuti Sakit' => 3,
                    'Cuti Ibadah' => 3,
                    'Cuti Keluarga' => 3,
                    default => 0,
                },
                'keterangan' => match ($name) {
                    'Cuti Tahunan' => 'Hak minimal 12 hari kerja setelah 1 tahun masa kerja.',
                    'Cuti Sakit' => 'Cuti sakit dengan surat dokter bila diperlukan.',
                    'Cuti Ibadah' => 'Cuti untuk ibadah hari besar.',
                    'Cuti Keluarga' => 'Cuti karena kondisi darurat keluarga.',
                    default => null,
                },
            ])->id;
        }

        // Data Leave Request - 7 records only
        $leaveRequests = [
            [
                'user_id' => $users->random()->id,
                'leave_type_id' => $leaveTypes['Cuti Tahunan'],
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(14),
                'total_days' => 5,
                'reason' => 'Liburan keluarga ke Bali dan refreshing',
                'status' => 'approved',
                'approved_by' => $users->random()->id,
                'approval_notes' => 'Disetujui dengan syarat: Pastikan semua project diserahkan dengan baik.',
            ],
            [
                'user_id' => $users->random()->id,
                'leave_type_id' => $leaveTypes['Cuti Sakit'],
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->subDays(1),
                'total_days' => 3,
                'reason' => 'Sakit demam dan flu, perlu istirahat di rumah',
                'status' => 'approved',
                'approved_by' => $users->random()->id,
                'approval_notes' => 'Cepat sembuh. Harap sertakan surat dokter jika lebih dari 3 hari.',
            ],
            [
                'user_id' => $users->random()->id,
                'leave_type_id' => $leaveTypes['Cuti Ibadah'],
                'start_date' => Carbon::now()->addDays(20),
                'end_date' => Carbon::now()->addDays(21),
                'total_days' => 2,
                'reason' => 'Ibadah haji bersama keluarga',
                'status' => 'pending',
                'approved_by' => null,
                'approval_notes' => null,
            ],
            [
                'user_id' => $users->random()->id,
                'leave_type_id' => $leaveTypes['Cuti Keluarga'],
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->subDays(5),
                'total_days' => 3,
                'reason' => 'Keadaan darurat keluarga - ayah masuk rumah sakit',
                'status' => 'approved',
                'approved_by' => $users->random()->id,
                'approval_notes' => 'Emergency disetujui langsung. Semoga semuanya baik-baik saja.',
            ],
            [
                'user_id' => $users->random()->id,
                'leave_type_id' => $leaveTypes['Cuti Tahunan'],
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(36),
                'total_days' => 7,
                'reason' => 'Liburan akhir tahun bersama keluarga besar',
                'status' => 'rejected',
                'approved_by' => $users->random()->id,
                'approval_notes' => 'Maaf, periode tersebut adalah peak season. Mohon pilih tanggal alternatif.',
            ],
            [
                'user_id' => $users->random()->id,
                'leave_type_id' => $leaveTypes['Cuti Sakit'],
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(6),
                'total_days' => 2,
                'reason' => 'Checkup kesehatan rutin dan pengobatan',
                'status' => 'pending',
                'approved_by' => null,
                'approval_notes' => null,
            ],
            [
                'user_id' => $users->random()->id,
                'leave_type_id' => $leaveTypes['Cuti Ibadah'],
                'start_date' => Carbon::now()->addDays(15),
                'end_date' => Carbon::now()->addDays(17),
                'total_days' => 3,
                'reason' => 'Menjalankan ibadah dan quality time dengan keluarga',
                'status' => 'approved',
                'approved_by' => $users->random()->id,
                'approval_notes' => 'Disetujui. Harap koordinasi dengan tim sebelum cuti.',
            ],
        ];

        foreach ($leaveRequests as $index => $leaveRequestData) {
            $leaveRequest = LeaveRequest::create($leaveRequestData);

            $statusIcon = match ($leaveRequestData['status']) {
                'approved' => '✅',
                'rejected' => '❌',
                'pending' => '⏳',
                default => '📝'
            };

            $leaveTypeName = array_search($leaveRequestData['leave_type_id'], $leaveTypes);

            echo '  '.($index + 1).". {$statusIcon} {$leaveTypeName} - {$leaveRequestData['status']}\n";
            echo "     👤 User ID: {$leaveRequestData['user_id']}\n";
            echo "     📅 {$leaveRequestData['start_date']} s/d {$leaveRequestData['end_date']} ({$leaveRequestData['total_days']} hari)\n";
            echo "     📝 {$leaveRequestData['reason']}\n\n";
        }

        echo "🎉 Successfully created 7 leave request records!\n\n";

        // Tampilkan statistik
        echo "📊 LEAVE REQUEST SUMMARY:\n";
        echo "─────────────────────────────────────\n";

        $pendingCount = collect($leaveRequests)->where('status', 'pending')->count();
        $approvedCount = collect($leaveRequests)->where('status', 'approved')->count();
        $rejectedCount = collect($leaveRequests)->where('status', 'rejected')->count();

        echo "📈 Total Leave Requests: 7\n";
        echo "⏳ Pending: {$pendingCount}\n";
        echo "✅ Approved: {$approvedCount}\n";
        echo "❌ Rejected: {$rejectedCount}\n\n";

        echo "🏖️ Leave request seeder completed successfully!\n";
    }
}
