# Blood Bank Management System — Security Fixes (Apinaja)

**Member:** Apinaja (GitHub: `Apinaja08`)
**Working branch:** `Member/Apinaja`
**Vulnerabilities covered:** Broken Access Control (IDOR), Cross-Site Request Forgery (CSRF)

---

## 1. Summary of My Contribution

| # | Vulnerability | Branch | Key commits |
|---|---------------|--------|-------------|
| 1 | Broken Access Control / IDOR | `vulnerability/Broken-_Access-_Control` | `6f2516d` Find the Broken Access Control in source code<br>`ed7740a` Fixed broken access control in action endpoints |
| 2 | Missing CSRF Tokens | `vulnerability/Missing-_CSRF-_Tokens` | `92115f8` Added CSRF token generation and server-side validation |
| 3 | Integration | `Member/Apinaja` | `58be62b` Merge Broken Access Control<br>`2ddd3d3` Merge Missing CSRF Tokens |

`Member/Apinaja` is built on top of Nikshan's branch (SQL injection / input validation fixes), so the final code combines all three layers of protection:
**prepared statements + ownership checks + CSRF tokens.**

```
main (ebd3746)
 ├── Nikshan: SQL injection & input validation fixes ──┐
 ├── vulnerability/Broken-_Access-_Control (mine) ─────┼──► Member/Apinaja
 └── vulnerability/Missing-_CSRF-_Tokens (mine) ───────┘
```

---

## 2. Vulnerability 1 — Broken Access Control (IDOR)

### 2.1 Problem

The action scripts in `file/` changed or deleted records using only an ID taken from the URL. They did not check:

- whether the user was logged in,
- whether the user had the correct role (hospital vs. receiver),
- whether the record belonged to that user.

Example of the original code (`file/accept.php`):

```php
$reqid = $_GET['reqid'];
$sql = "update bloodrequest SET status = '$status' WHERE reqid = '$reqid'";
```

Any person could open `file/accept.php?reqid=5` and accept another hospital's request, or change the ID in
`file/delete.php?bid=…` to delete another hospital's blood stock.

### 2.2 Affected Files

| File | Action | Who should be allowed |
|------|--------|-----------------------|
| `file/accept.php` | Accept a blood request | Hospital that received the request |
| `file/reject.php` | Reject a blood request | Hospital that received the request |
| `file/acceptd.php` | Accept a donation request | Receiver the request was sent to |
| `file/rejectd.php` | Reject a donation request | Receiver the request was sent to |
| `file/cancel.php` | Cancel a blood request | Receiver who sent it |
| `file/canceld.php` | Cancel a donation request | Hospital that sent it |
| `file/delete.php` | Delete a blood sample | Hospital that owns it |
| `file/deleted.php` | Delete a donor sample | Receiver who owns it |
| `file/request.php` | Send a blood request | Logged-in receiver |
| `file/requestd.php` | Send a donation request | Logged-in hospital |

### 2.3 Fix

**New helper — `file/auth.php`**

```php
function require_role($role) {          // 'hid' = hospital, 'rid' = receiver
    if (!isset($_SESSION[$role])) {
        header('location:../login.php');
        exit;
    }
    return (int) $_SESSION[$role];
}

function require_id($name, $redirect) { // positive integer only
    $id = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT,
                       array('options' => array('min_range' => 1)));
    if (!$id) { header("location:../".$redirect."?error=Invalid request."); exit; }
    return $id;
}
```

**Ownership enforced in the SQL `WHERE` clause** — the owner ID comes from the session, never from the user:

```php
$hid   = require_role('hid');
$reqid = require_id('reqid', 'bloodrequest.php');
$stmt  = $conn->prepare("UPDATE bloodrequest SET status = ? WHERE reqid = ? AND hid = ?");
$stmt->bind_param("sii", $status, $reqid, $hid);
if ($stmt->execute() && $stmt->affected_rows > 0) { /* success */ }
else { /* "Request not found or you are not allowed to change it." */ }
```

**Request forms no longer trust hidden fields** — `request.php` / `requestd.php` take only the sample ID (`bid` / `bdid`)
and read the real `hid`, `rid` and blood group from the database.

### 2.4 Result

- Unauthenticated users are redirected to the login page.
- A hospital cannot act on receiver endpoints and vice versa.
- Changing an ID to someone else's record has no effect (`affected_rows = 0`) and shows an error.

---

## 3. Vulnerability 2 — Missing CSRF Tokens

### 3.1 Problem

All state-changing actions (accept, reject, cancel, delete, add sample, send request, update profile) were triggered by
plain links or forms with no token. A malicious website could make a logged-in user's browser send these requests
without their knowledge, for example:

```html
<img src="http://localhost:8080/file/delete.php?bid=3">
```

### 3.2 Fix

**New helper — `file/csrf.php`**

```php
function csrf_token() {               // one random token per session
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {               // hidden input for every POST form
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

function csrf_verify($redirect) {     // constant-time comparison
    $sent = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $sent)) {
        header("location:../" . $redirect . "?error=Invalid or missing CSRF token.");
        exit;
    }
}
```

**GET links replaced with POST forms** carrying the token, e.g. in `bloodrequest.php`:

```php
<form action="file/accept.php" method="post" style="display:inline">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="reqid" value="<?php echo $row['reqid']; ?>">
    <button type="submit" class="btn btn-success">Accept</button>
</form>
```

**Every handler verifies the token** before doing any work (`csrf_verify('bloodrequest.php');`).

### 3.3 Coverage

| Page (form) | Handler | Token verified |
|-------------|---------|----------------|
| `bloodrequest.php` | `accept.php`, `reject.php` | ✅ |
| `blooddonate.php` | `acceptd.php`, `rejectd.php` | ✅ |
| `sentrequest.php` | `cancel.php` | ✅ |
| `sentrequestd.php` | `canceld.php` | ✅ |
| `bloodinfo.php` | `infoAdd.php`, `delete.php` | ✅ |
| `blooddinfo.php` | `infoAddd.php`, `deleted.php` | ✅ |
| `abs.php` | `request.php` | ✅ |
| `deleteit.php` | `requestd.php` | ✅ |
| `rprofile.php`, `hprofile.php` | `updateprofile.php` | ✅ |
| `login.php` (hospital & user) | `hospitalLogin.php`, `receiverLogin.php` | ✅ |
| `register.php` (hospital & user) | `hospitalReg.php`, `receiverReg.php` | ✅ |

---

## 4. Merging Into `Member/Apinaja`

Both of my branches were created from `main`, while `Member/Apinaja` already contained Nikshan's SQL injection fixes,
so the merges produced conflicts in the same files. They were resolved by **keeping every protection from each side**:

| Merge | Conflicts | Resolution |
|-------|-----------|------------|
| Broken Access Control (`58be62b`) | 9 action files | Kept the access-control version (prepared statements + ownership check) and added `urlencode()` on redirect messages from Nikshan's version. |
| Missing CSRF Tokens (`2ddd3d3`) | 15 files | Kept the secured code (prepared statements, validation, ownership), then added `require 'csrf.php'`, `csrf_verify()` and `csrf_field()`. `require_id()` was switched from `GET` to `POST` to match the new forms. |

---

## 5. How to Test

1. Start the project: `docker compose up --build`, then open <http://localhost:8080>.
2. **Access control**
   - Log out and open `http://localhost:8080/file/accept.php` → redirected to login.
   - Log in as a receiver and submit to `file/accept.php` → redirected to login (wrong role).
   - Log in as Hospital A and try to accept a request belonging to Hospital B (edit the hidden `reqid` in DevTools) →
     *"Request not found or you are not allowed to change it."*
3. **CSRF**
   - Remove or change the `csrf_token` hidden field in DevTools and submit → *"Invalid or missing CSRF token."*
   - Submit a form from another origin/page without the token → rejected.
4. Normal use (accept, reject, cancel, delete, add sample, request, update profile) still works.

---

## 6. Remaining Items (Out of My Scope)

- `file/oauth_config.php` contains a hard-coded Google client secret, which should be moved to an environment variable and rotated.
- Password hashing is handled in the `Sarumathy` branch, which is not yet merged into `Member/Apinaja`.
