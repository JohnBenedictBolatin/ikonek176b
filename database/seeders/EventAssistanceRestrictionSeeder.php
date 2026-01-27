<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventAssistanceRestriction;

class EventAssistanceRestrictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Maps offenses to event assistance restrictions based on logical relationships.
     */
    public function run(): void
    {
        $restrictions = [
            // MEDIUM SEVERITY OFFENSES
            
            // Violation of Community Rules (Repeated) - restricts facility reservations
            ['event_type' => 'Court Reservation', 'offense_type' => 'Violation of Community Rules (Repeated)', 'severity_level' => 'Medium'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Violation of Community Rules (Repeated)', 'severity_level' => 'Medium'],
            
            // Noise Disturbance (Repeated) - restricts facilities and sound system
            ['event_type' => 'Court Reservation', 'offense_type' => 'Noise Disturbance (Repeated/Second Offense)', 'severity_level' => 'Medium'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Noise Disturbance (Repeated/Second Offense)', 'severity_level' => 'Medium'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Noise Disturbance (Repeated/Second Offense)', 'severity_level' => 'Medium'],
            
            // Improper Waste Disposal (Repeated) - restricts facilities
            ['event_type' => 'Court Reservation', 'offense_type' => 'Improper Waste Disposal (Repeated/Second Offense)', 'severity_level' => 'Medium'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Improper Waste Disposal (Repeated/Second Offense)', 'severity_level' => 'Medium'],
            
            // Boundary Disputes (Unresolved) - restricts community hall
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Boundary Disputes (Unresolved/Formal Complaint)', 'severity_level' => 'Medium'],
            
            // Public Nuisance (Moderate) - restricts facilities
            ['event_type' => 'Court Reservation', 'offense_type' => 'Public Nuisance (Moderate)', 'severity_level' => 'Medium'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Public Nuisance (Moderate)', 'severity_level' => 'Medium'],
            
            // Non-payment of Community Dues (Significant) - restricts all
            ['event_type' => 'Court Reservation', 'offense_type' => 'Non-payment of Community Dues (Significant Amount/Repeated)', 'severity_level' => 'Medium'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Non-payment of Community Dues (Significant Amount/Repeated)', 'severity_level' => 'Medium'],
            ['event_type' => 'Manpower Assistance', 'offense_type' => 'Non-payment of Community Dues (Significant Amount/Repeated)', 'severity_level' => 'Medium'],
            ['event_type' => 'Tent and Tables Borrowing', 'offense_type' => 'Non-payment of Community Dues (Significant Amount/Repeated)', 'severity_level' => 'Medium'],
            ['event_type' => 'Sports Equipment Borrowing', 'offense_type' => 'Non-payment of Community Dues (Significant Amount/Repeated)', 'severity_level' => 'Medium'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Non-payment of Community Dues (Significant Amount/Repeated)', 'severity_level' => 'Medium'],
            
            // Property Damage (₱5,000 - ₱20,000) - restricts equipment borrowing
            ['event_type' => 'Sports Equipment Borrowing', 'offense_type' => 'Property Damage (₱5,000 - ₱20,000)', 'severity_level' => 'Medium'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Property Damage (₱5,000 - ₱20,000)', 'severity_level' => 'Medium'],
            ['event_type' => 'Tent and Tables Borrowing', 'offense_type' => 'Property Damage (₱5,000 - ₱20,000)', 'severity_level' => 'Medium'],
            
            // Unauthorized Construction (Minor) - restricts facilities
            ['event_type' => 'Court Reservation', 'offense_type' => 'Unauthorized Construction (Minor - without permit but not illegal)', 'severity_level' => 'Medium'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Unauthorized Construction (Minor - without permit but not illegal)', 'severity_level' => 'Medium'],
            
            // Disruptive Behavior in Community Events - restricts facilities and manpower
            ['event_type' => 'Court Reservation', 'offense_type' => 'Disruptive Behavior in Community Events', 'severity_level' => 'Medium'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Disruptive Behavior in Community Events', 'severity_level' => 'Medium'],
            ['event_type' => 'Manpower Assistance', 'offense_type' => 'Disruptive Behavior in Community Events', 'severity_level' => 'Medium'],
            
            // Violation of Barangay Ordinances (Moderate) - restricts facilities
            ['event_type' => 'Court Reservation', 'offense_type' => 'Violation of Barangay Ordinances (Moderate)', 'severity_level' => 'Medium'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Violation of Barangay Ordinances (Moderate)', 'severity_level' => 'Medium'],
            
            // HIGH SEVERITY OFFENSES
            
            // Illegal Construction (Major) - restricts all facilities
            ['event_type' => 'Court Reservation', 'offense_type' => 'Illegal Construction (Major/Without Permit)', 'severity_level' => 'High'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Illegal Construction (Major/Without Permit)', 'severity_level' => 'High'],
            
            // Public Nuisance (Severe/Repeated) - restricts facilities and sound system
            ['event_type' => 'Court Reservation', 'offense_type' => 'Public Nuisance (Severe/Repeated)', 'severity_level' => 'High'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Public Nuisance (Severe/Repeated)', 'severity_level' => 'High'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Public Nuisance (Severe/Repeated)', 'severity_level' => 'High'],
            
            // Property Damage (Over ₱20,000) - restricts all equipment
            ['event_type' => 'Sports Equipment Borrowing', 'offense_type' => 'Property Damage (Over ₱20,000)', 'severity_level' => 'High'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Property Damage (Over ₱20,000)', 'severity_level' => 'High'],
            ['event_type' => 'Tent and Tables Borrowing', 'offense_type' => 'Property Damage (Over ₱20,000)', 'severity_level' => 'High'],
            
            // Fraudulent Document Request - restricts all
            ['event_type' => 'Court Reservation', 'offense_type' => 'Fraudulent Document Request', 'severity_level' => 'High'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Fraudulent Document Request', 'severity_level' => 'High'],
            ['event_type' => 'Manpower Assistance', 'offense_type' => 'Fraudulent Document Request', 'severity_level' => 'High'],
            ['event_type' => 'Tent and Tables Borrowing', 'offense_type' => 'Fraudulent Document Request', 'severity_level' => 'High'],
            ['event_type' => 'Sports Equipment Borrowing', 'offense_type' => 'Fraudulent Document Request', 'severity_level' => 'High'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Fraudulent Document Request', 'severity_level' => 'High'],
            
            // Misuse of Barangay Services - restricts all
            ['event_type' => 'Court Reservation', 'offense_type' => 'Misuse of Barangay Services', 'severity_level' => 'High'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Misuse of Barangay Services', 'severity_level' => 'High'],
            ['event_type' => 'Manpower Assistance', 'offense_type' => 'Misuse of Barangay Services', 'severity_level' => 'High'],
            ['event_type' => 'Tent and Tables Borrowing', 'offense_type' => 'Misuse of Barangay Services', 'severity_level' => 'High'],
            ['event_type' => 'Sports Equipment Borrowing', 'offense_type' => 'Misuse of Barangay Services', 'severity_level' => 'High'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Misuse of Barangay Services', 'severity_level' => 'High'],
            
            // Violation of Community Rules (Severe/Repeated) - restricts all
            ['event_type' => 'Court Reservation', 'offense_type' => 'Violation of Community Rules (Severe/Repeated)', 'severity_level' => 'High'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Violation of Community Rules (Severe/Repeated)', 'severity_level' => 'High'],
            ['event_type' => 'Manpower Assistance', 'offense_type' => 'Violation of Community Rules (Severe/Repeated)', 'severity_level' => 'High'],
            ['event_type' => 'Tent and Tables Borrowing', 'offense_type' => 'Violation of Community Rules (Severe/Repeated)', 'severity_level' => 'High'],
            ['event_type' => 'Sports Equipment Borrowing', 'offense_type' => 'Violation of Community Rules (Severe/Repeated)', 'severity_level' => 'High'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Violation of Community Rules (Severe/Repeated)', 'severity_level' => 'High'],
            
            // Boundary Disputes (Legal Action Pending) - restricts facilities
            ['event_type' => 'Court Reservation', 'offense_type' => 'Boundary Disputes (Legal Action Pending)', 'severity_level' => 'High'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Boundary Disputes (Legal Action Pending)', 'severity_level' => 'High'],
            
            // Threatening Behavior or Harassment - restricts all, especially manpower
            ['event_type' => 'Court Reservation', 'offense_type' => 'Threatening Behavior or Harassment', 'severity_level' => 'High'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Threatening Behavior or Harassment', 'severity_level' => 'High'],
            ['event_type' => 'Manpower Assistance', 'offense_type' => 'Threatening Behavior or Harassment', 'severity_level' => 'High'],
            ['event_type' => 'Tent and Tables Borrowing', 'offense_type' => 'Threatening Behavior or Harassment', 'severity_level' => 'High'],
            ['event_type' => 'Sports Equipment Borrowing', 'offense_type' => 'Threatening Behavior or Harassment', 'severity_level' => 'High'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Threatening Behavior or Harassment', 'severity_level' => 'High'],
            
            // Destruction of Public Property - restricts all equipment and facilities
            ['event_type' => 'Court Reservation', 'offense_type' => 'Destruction of Public Property', 'severity_level' => 'High'],
            ['event_type' => 'Community Hall Reservation', 'offense_type' => 'Destruction of Public Property', 'severity_level' => 'High'],
            ['event_type' => 'Sports Equipment Borrowing', 'offense_type' => 'Destruction of Public Property', 'severity_level' => 'High'],
            ['event_type' => 'Sound System Borrowing', 'offense_type' => 'Destruction of Public Property', 'severity_level' => 'High'],
            ['event_type' => 'Tent and Tables Borrowing', 'offense_type' => 'Destruction of Public Property', 'severity_level' => 'High'],
        ];

        foreach ($restrictions as $restriction) {
            EventAssistanceRestriction::updateOrCreate(
                [
                    'event_type' => $restriction['event_type'],
                    'offense_type' => $restriction['offense_type'],
                ],
                [
                    'severity_level' => $restriction['severity_level'],
                ]
            );
        }

        $this->command->info('Event assistance restrictions seeded successfully.');
    }
}
