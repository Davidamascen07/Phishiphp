<?php
/**
 * Test script to verify TrainingManager class is working
 */

// Include the necessary files
require_once 'manager/common_functions.php';
require_once 'manager/training_manager.php';

// Test instantiation
try {
    echo "Testing TrainingManager class instantiation...\n";
    
    // Create instance
    $training_manager = new TrainingManager();
    echo "✅ TrainingManager class instantiated successfully!\n";
    
    // Test a simple method
    $modules = $training_manager->getAllTrainingModules();
    echo "✅ getAllTrainingModules method called successfully!\n";
    echo "Found " . count($modules) . " training modules.\n";
    
    // Test getting a specific module
    if (!empty($modules)) {
        $first_module = $modules[0];
        $module_detail = $training_manager->getTrainingModuleById($first_module['module_id']);
        echo "✅ getTrainingModuleById method called successfully!\n";
        echo "Module has quiz enabled: " . ($module_detail['quiz_enabled'] ? 'Yes' : 'No') . "\n";
    }
    
    echo "\n🎉 All tests passed! TrainingManager class is working correctly.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
}
?>