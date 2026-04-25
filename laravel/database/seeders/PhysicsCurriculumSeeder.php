<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhysicsCurriculumSeeder extends Seeder
{
    public function run(): void
    {
        // Resolve institute via the admin user
        $user = DB::table('users')->where('username', 'Shrisitaram')->first();

        if (! $user) {
            $this->command->error('User Shrisitaram not found. Run AdminUserSeeder first.');
            return;
        }

        $instituteId = $user->institute_id;

        // Ensure Physics subject exists
        $subject = DB::table('subjects')
            ->where('institute_id', $instituteId)
            ->whereRaw('LOWER(name) = ?', ['physics'])
            ->first();

        if (! $subject) {
            $subjectId = DB::table('subjects')->insertGetId([
                'institute_id'  => $instituteId,
                'name'          => 'Physics',
                'code'          => 'P',
                'display_order' => 1,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $this->command->info('Created Physics subject (code: P).');
        } else {
            $subjectId = $subject->id;
            $this->command->info("Using existing Physics subject (id: {$subjectId}).");
        }

        // 45 real topics from coaching institute visit — typos kept as-is (actual data)
        $topics = [
            1  => 'Calorimetry',
            2  => 'Thermodynamics',
            3  => 'Kinetic theory of gases',
            4  => 'Thermodynamics',
            5  => 'Heat Transfer',
            6  => 'Alternating Current',
            7  => 'Alternating Current, Magnetics, Electrostatics',
            8  => 'Electro Magnetics Waves',
            9  => 'Electric Current',
            10 => 'Electrostatics, Magnetics',
            11 => 'Magnetics',
            12 => 'Capacitance',
            13 => 'KVL, KCL',
            14 => 'Electrostatics',
            15 => 'Electrostatics',
            16 => 'Unit & Measurement',
            17 => 'Error Analysis',
            18 => 'Kinematics 2-D Motion',
            19 => "Newton's Laws of Motion",
            20 => "Newton's Laws of Motion",
            21 => 'Work Energy Power',
            22 => 'Momentum Conservation',
            23 => 'Momentum',
            24 => 'Inertia',
            25 => 'Gravitation',
            26 => 'Elasticity',
            27 => 'Surface Tension',
            28 => 'Fulid Mechanics',
            29 => "Stoke's Laws",
            30 => 'Electrostatics, SHM',
            31 => 'Ray Optics',
            32 => 'Optical Insturment',
            33 => 'YDSE',
            34 => 'Logic Gate',
            35 => 'Modren Physics, Electrostatics',
            36 => 'Optical Insturment',
            37 => "Bohr's Model",
            38 => 'Resonance',
            39 => 'Semiconducter',
            40 => 'Nuclear Physics',
            41 => 'Semiconducter',
            42 => 'Waves Theory',
            43 => 'Photo electric effect',
            44 => 'Optical Insturment',
            45 => 'Electro Magnetics Waves',
        ];

        $inserted = 0;
        $skipped  = 0;

        foreach ($topics as $seq => $name) {
            $code     = 'P-' . str_pad($seq, 3, '0', STR_PAD_LEFT); // P-001 … P-045
            $fullCode = $code;

            // Skip if this full_code already exists for this institute
            if (DB::table('curriculum_nodes')->where('institute_id', $instituteId)->where('full_code', $fullCode)->exists()) {
                $this->command->warn("  Skip #{$seq} '{$name}' — code {$fullCode} already exists.");
                $skipped++;
                continue;
            }

            DB::table('curriculum_nodes')->insert([
                'institute_id'  => $instituteId,
                'subject_id'    => $subjectId,
                'parent_id'     => null,
                'level'         => 'chapter',
                'name'          => $name,
                'code'          => $code,
                'full_code'     => $fullCode,
                'display_order' => $seq,
                'weightage'     => null,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $this->command->line("  ✓ #{$seq} [{$fullCode}] {$name}");
            $inserted++;
        }

        $this->command->info("Done. Inserted: {$inserted}, Skipped (duplicates): {$skipped}.");
    }
}
