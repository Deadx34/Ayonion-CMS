# Auto Carry Forward System Revamp - Implementation Guide

## Changes Completed in index.html:

### 1. UI Changes:
✅ Removed the "Auto Carry Forward System" card from the dashboard (lines 290-345)
✅ Added "Subscription Duration (Months)" field to Add Client modal
✅ Added "Subscription Duration (Months)" field to Edit Client modal

### 2. JavaScript Changes:
✅ Updated `addClientForm` submission to include `subscriptionMonths`
✅ Updated `showEditClientModal` to populate `editSubscriptionMonths`
✅ Updated `editClientForm` submission to include `subscriptionMonths`
✅ Replaced manual `runAutoCarryForward()` with automated `checkAndRunAutoCarryForward()`
✅ Added automatic carry forward check to `initializeApp()`

## Changes Still Needed in index.php:

### 1. Add Subscription Duration Field to Add Client Modal (around line 950):
```php
<div class="col-md-6 mb-3">
    <label class="form-label">Monthly Renewal Date</label>
    <input type="date" class="form-control" id="renewalDate" required>
    <small class="text-muted">First renewal date for this client</small>
</div>
<div class="col-md-6 mb-3">
    <label class="form-label">Subscription Duration (Months)</label>
    <input type="number" class="form-control" id="subscriptionMonths" required min="1" value="12">
    <small class="text-muted">Auto carry forward will work for this duration</small>
</div>
```

### 2. Add Subscription Duration Field to Edit Client Modal (around line 793):
```php
<div class="col-md-6 mb-3">
    <label class="form-label">Monthly Renewal Date</label>
    <input type="date" class="form-control" id="editRenewalDate" required>
</div>
<div class="col-md-6 mb-3">
    <label class="form-label">Subscription Duration (Months)</label>
    <input type="number" class="form-control" id="editSubscriptionMonths" required min="1">
    <small class="text-muted">Auto carry forward will work for this duration</small>
</div>
```

### 3. Remove Auto Carry Forward Card from Dashboard (around line 292-345)

### 4. Update JavaScript Functions:
- Add `subscriptionMonths` to addClientForm submission
- Add `subscriptionMonths` to editClientForm submission
- Populate `editSubscriptionMonths` in showEditClientModal
- Replace manual carry forward functions with automated check
- Add `checkAndRunAutoCarryForward()` to initializeApp

## Backend Changes Required (handler_clients.php):

### 1. Update Database Schema:
Add new columns to `clients` table:
```sql
ALTER TABLE clients 
ADD COLUMN subscription_months INT DEFAULT 12,
ADD COLUMN subscription_start_date DATE,
ADD COLUMN subscription_end_date DATE,
ADD COLUMN last_carry_forward_date DATE;
```

### 2. Update Add Client Action:
```php
case 'add':
    $partnerId = $data->partnerId ?? '';
    $companyName = $data->companyName ?? '';
    $renewalDate = $data->renewalDate ?? date('Y-m-d');
    $subscriptionMonths = $data->subscriptionMonths ?? 12;
    $packageCredits = $data->packageCredits ?? 0;
    $managingPlatforms = $data->managingPlatforms ?? '';
    $industry = $data->industry ?? '';
    $logoUrl = $data->logoUrl ?? '';
    
    // Calculate subscription end date
    $subscriptionStartDate = $renewalDate;
    $subscriptionEndDate = date('Y-m-d', strtotime($renewalDate . " +{$subscriptionMonths} months"));
    
    $sql = "INSERT INTO clients (partnerId, companyName, renewalDate, subscriptionMonths, subscription_start_date, subscription_end_date, packageCredits, extraCredits, usedCredits, carriedForwardCredits, managingPlatforms, industry, logoUrl, createdAt) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, 0, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssisssss', $partnerId, $companyName, $renewalDate, $subscriptionMonths, $subscriptionStartDate, $subscriptionEndDate, $packageCredits, $managingPlatforms, $industry, $logoUrl);
```

### 3. Update Edit Client Action:
Include `subscriptionMonths` in the UPDATE query and recalculate `subscription_end_date`.

### 4. Add Automated Carry Forward Action:
```php
case 'auto_carry_forward':
    $today = date('Y-m-d');
    $results = [];
    $processed = 0;
    
    // Get clients whose renewal date has passed and subscription is still active
    $sql = "SELECT * FROM clients WHERE renewalDate <= ? AND subscription_end_date >= ? AND (last_carry_forward_date IS NULL OR last_carry_forward_date < renewalDate)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $today, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($client = $result->fetch_assoc()) {
        $clientId = $client['id'];
        $currentRenewal = $client['renewalDate'];
        
        // Calculate unused credits
        $totalCredits = $client['packageCredits'] + $client['extraCredits'] + $client['carriedForwardCredits'];
        $unusedCredits = $totalCredits - $client['usedCredits'];
        
        if ($unusedCredits > 0) {
            // Move to next month
            $newRenewalDate = date('Y-m-d', strtotime($currentRenewal . ' +1 month'));
            
            // Update client: carry forward unused credits, reset used credits, update renewal date
            $updateSql = "UPDATE clients SET 
                         carriedForwardCredits = ?,
                         usedCredits = 0,
                         renewalDate = ?,
                         last_carry_forward_date = ?
                         WHERE id = ?";
            
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param('issi', $unusedCredits, $newRenewalDate, $today, $clientId);
            
            if ($updateStmt->execute()) {
                $processed++;
                $results[] = [
                    'client_id' => $clientId,
                    'client_name' => $client['companyName'],
                    'carried_forward' => $unusedCredits,
                    'new_renewal_date' => $newRenewalDate
                ];
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'processed_count' => $processed,
        'results' => $results
    ]);
    break;
```

## Key Features of New System:

1. **Automatic Processing**: Runs on every page load, no manual button needed
2. **Subscription-Based**: Carry forward only works within subscription period
3. **Date Tracking**: Tracks last carry forward to prevent duplicates
4. **Silent Operation**: Processes in background without user interaction
5. **Renewable**: When admin extends subscription, carry forward resumes automatically

## Testing Steps:

1. Add a new client with subscription duration
2. Set renewal date to yesterday
3. Reload the page
4. Check that credits carried forward and renewal date moved to next month
5. Verify subscription end date stops carry forward
6. Test renewing subscription by updating subscription months

## Notes:

- Carry forward will only happen once per renewal period
- System checks subscription_end_date to ensure client is still subscribed
- Admin can renew by editing client and updating subscription months
- All carry forwards are logged silently in console
