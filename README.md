# 🩸 Blood Bank Management System

A web application that acts as a bridge between **hospitals/clinics** and **blood donors/receivers**. It simplifies the process of blood donation, requests, and inventory management.

---

## 🚀 Features

### 👨‍⚕️ For Hospitals/Clinics (Doctors)

- Add blood samples to the blood bank
- Request blood units
- Track the status of requests
- Update hospital details

### 🧑‍🤝‍🧑 For Donors/Receivers

- Register and manage user profile
- Donate or request blood
- Track donation/request history

---

## 💠 Technologies Used

| Layer        | Tools Used                       |
| ------------ | -------------------------------- |
| Frontend     | HTML, CSS, Bootstrap, JavaScript |
| Backend      | PHP                              |
| Database     | MySQL                            |
| DevOps/Infra | Docker, Docker Compose           |

---

## 📦 Requirements

- [Docker](https://docs.docker.com/get-docker/) – Download and install Docker for your OS
- [Docker Compose](https://docs.docker.com/compose/install/) – Install Docker Compose if not bundled with Docker Desktop
- Code Editor (e.g. VS Code, Sublime Text)

---

## 🧪 Getting Started

1. **Clone the repository:**

   ```bash
   git clone https://github.com/Shridharshukl/Blood-Bank-Management-System.git
   cd Blood-Bank-Management-System
   ```

2. **Start the application with Docker Compose:**

   ```bash
   docker-compose up --build
   ```

3. **Access the app:** Open your browser and visit [http://localhost:8000](http://localhost:8000)

---

## 📁 Project Structure

| Folder/File          | Description                                  |
| -------------------- | -------------------------------------------- |
| `css/`               | Frontend stylesheets (CSS)                   |
| `file/`              | Backend PHP logic and database connections   |
| `image/`             | Images used in frontend                      |
| `jastimage/`         | Additional image assets                      |
| `sql/`               | SQL file to auto-import into MySQL container |
| `docker-compose.yml` | Defines Docker services                      |

---

## 📊 Architecture Diagram

<p align="center">
  <img src="./jastimage/arch.png" alt="Architecture Diagram" width="600"/>
</p>

---

## 🔄 How It Works (Process Flow)

1. **User (Donor or Hospital) logs in** via the web interface.
2. **Donors** can donate blood, view donation history, or update profiles.
3. **Hospitals/Clinics** can:
   - Add available blood samples to the system
   - Request blood based on patient needs
   - View the status of their blood requests
4. **PHP** handles request processing and interacts with **MySQL** to retrieve/store data.
5. All content is served via **Apache**, running inside Docker containers.
6. The browser acts as the main interface for interaction between the users and the server.

---

## Broken Access Control Findings

Broken Access Control occurs when a user can access or modify another user's data or perform an action without the required role or ownership check.

### Affected Files

| File | Issue |
| ---- | ----- |
| `file/accept.php` | Changes any blood request to `Accepted` using only the client-provided `reqid`. It does not verify a hospital session or request ownership. |
| `file/acceptd.php` | Accepts any donation using only the client-provided `donoid`, without authentication or ownership validation. |
| `file/reject.php` | Changes any blood request to `Rejected` using only `reqid`, without checking the logged-in hospital. |
| `file/rejectd.php` | Rejects any donation using only `donoid`, without checking the logged-in hospital. |
| `file/cancel.php` | Deletes any blood request when an attacker supplies its `reqid`; there is no receiver authentication or ownership check. |
| `file/canceld.php` | Deletes any donation request using only `donoid`, without verifying the logged-in receiver. |
| `file/delete.php` | Deletes any hospital blood-inventory record using only `bid`; it does not verify that the record belongs to the current hospital. |
| `file/deleted.php` | Deletes any donor blood-inventory record using only `bdid`; it does not verify that the record belongs to the current receiver. |
| `file/request.php` | Trusts submitted `hid`, `rid`, and `bg` values without verifying that the referenced records and relationships are valid. |
| `file/requestd.php` | Trusts submitted `hid`, `rid`, and `bg` values without verifying that the referenced records and relationships are valid. |

### Impact

An unauthenticated attacker, or a logged-in user with a different role, may guess or change an ID in a URL and accept, reject, cancel, or delete another user's records. This is an Insecure Direct Object Reference (IDOR) and a Broken Access Control vulnerability.

### Required Fixes

- Start the session and require authentication in every protected endpoint.
- Enforce role checks: only hospitals may manage hospital requests, and only receivers/donors may manage their own records.
- Check ownership in the SQL `WHERE` clause using the session ID, for example `hid = $_SESSION['hid']` or `rid = $_SESSION['rid']`.
- Do not trust posted or URL-provided owner IDs; derive the acting user's ID from the authenticated session.
- Use `POST` for state-changing actions and add CSRF protection.
- Use prepared statements for all database queries.

The files above document the current Broken Access Control locations. This section is an assessment; adding it to the README does not itself fix the vulnerabilities.

---

## ❤️ Like this project?

If you found this project helpful, consider giving it a ⭐ on GitHub — it motivates and supports open-source contributions!
