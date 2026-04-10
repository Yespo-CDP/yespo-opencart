# Yespo CDP Integration for OpenCart

The official integration module for connecting OpenCart 2.3.x and 3.0.x stores with [Yespo CDP](https://yespo.io/). 

Unlike manual setups, this module is built on a **"Zero-Configuration"** architecture. Once you provide your API key, the module autonomously handles validation, injects web tracking, web push, and initiates parallel synchronization of your contacts and orders.

---

## ⚙️ API Architecture & Detailed Data Flow

This module is designed to work silently and efficiently in the background. Below is the detailed breakdown of all API methods, payloads, and error handling mechanisms.

### 1. API Key Validation & Initialization
* **Method:** `GET /api/v1/account/info`
* **Trigger:** User enters the API Key in the module settings and clicks "Synchronize".
* **Happy Flow:** The API returns account metadata including `orgId` and `organisationName`. The module saves these credentials to OpenCart's settings, updates the UI to confirm the connection, and triggers the success log.
* **Error Handling:** If validation fails (e.g., 401 Unauthorized), the synchronization halts immediately. The UI displays an error to the admin, and the system records an error in the dedicated integration log.

### 2. UI Process Flow & Auto-Recovery Mechanism:
Once the API key is successfully validated, the frontend interface orchestrates a fully automated setup sequence:
* **Parallel Execution:** The UI instantly triggers three asynchronous AJAX processes in parallel:
  1. Web Tracking Configuration (`getSiteScript`)
  2. Web Push Configuration (`addWebPush`)
  3. Historical Data Import (`loadCustomers` and `loadOrders`)
* **Live Status Tracking:** The interface transforms into a live dashboard, displaying real-time progress indicators (spinners, success checkmarks, or error icons) for each individual step. It dynamically tracks the total number of contacts and orders synced, updating the success and failure counts on the fly.
* **Automatic Retry (Auto-Recovery):** To handle temporary network hiccups or API rate limits, the frontend implements an automatic retry mechanism. If any process (tracking script retrieval, web push registration, or a specific data batch) fails or times out, the system automatically retries the failed request up to **5 times** (with a 3-second delay between attempts).
* **Manual Intervention:** If all 5 automatic retries are exhausted, the process halts, logs the final error, and presents a "Try Again" button. This allows the administrator to manually resume the exact failed step (e.g., resuming a historical data import from the exact page where it stopped) without restarting the entire configuration process.

### 3. Automated Web Tracking Setup
The module automatically configures Yespo's Web Tracking capabilities through a sequential API flow.
* **Step 1: Domain Registration**
	* **Method:** `POST /api/v1/site/domains`
	* **Payload:** The plugin sends the store's `domain`.
	* **Happy Flow:** The domain is successfully registered, and the API returns a unique Site ID. The system logs a success event.
* **Step 2: Script Retrieval & Implementation**
	* **Method:** `GET /api/v1/site/script`
	* **Trigger:** Initiated immediately upon successful domain registration.
	* **Happy Flow:** The API returns the text of the tracking script. The script is then dynamically injected after the storefront's `<body>` tag via OpenCart's OCMOD.
* **Error Handling:** Failures at any stage are intercepted. The system logs specific errors. Even if the OCMOD injection fails, the storefront continues to operate normally without breaking frontend performance. If the script installation fails the user will be able to try again.
* **Behavioral Events & Triggers:** After successful configuration, the following events are automatically tracked:
	* *Backend Events:* `StatusCart`, `PurchasedItems`, `CustomerData`, `AddToWishlist`.
	* *Frontend Events:* `CustomerData`, `StatusCartPage`, `MainPage`, `NotFound`, `ProductPage`, `SearchRequest`, `CategoryPage`.
* **Important Note on Product Variants:** Across all tracking events, only the primary `product_id` is utilized. OpenCart's core architecture treats product options (e.g., size, color) as modifiers attached to a main product, rather than standalone entities with distinct, uniquely identifiable IDs. Consequently, reliably extracting unique identifiers for specific option combinations to track them individually or to construct a comprehensive product feed for every variant is technically complex and unsupported natively without profound custom modifications.

### 4. Automated Web Push Configuration
The module automatically configures Yespo's Web Push capabilities through a sequential API flow.
* **Step 1: Domain Registration**
  * **Method:** `POST /api/v1/domain/web-push`
  * **Payload:** The plugin sends the store's `domain`, the intended `serviceWorkerName` (e.g., `sw-yespo.js`), the `serviceWorkerScope` (typically `/`) and the `serviceWorkerPath` (same as `serviceWorkerScope`).
  * **Happy Flow:** If Yespo successfully registers the domain, it logs a success event.
* **Step 2: Script & Service Worker Retrieval**
  * **Method:** `GET /api/v1/domain/web-push/script`
  * **Trigger:** Initiated immediately upon successful domain registration.
  * **Happy Flow:** The API returns both the HTML script snippet and the raw JavaScript content for the Service Worker.
* **Step 3: Implementation & File Generation**
  * **Service Worker Placement:** The plugin takes the raw Service Worker content returned by the API, automatically creates the physical file, and saves it directly to the root directory of your OpenCart installation.
  * **HTML Injection:** The retrieved script is dynamically injected after the storefront's `<body>` tag via OCMOD. This enables the native subscription prompt for visitors.
* **Error Handling:** Failures at any stage are intercepted. The system logs specific errors. If the script or service worker installation fails, the user will be able to try again.

### 5. Contact Synchronization (Real-time & Bulk)
* **Real-time Methods:** `POST /api/v1/contact` (Create/Update) and `DELETE /api/v1/contact` (Delete).
	* **Trigger:** OpenCart's native events (`customer/addCustomer/after`, `customer/editCustomer/after`, `customer/deleteCustomer/after`).
	* **Payload:** Mapped object containing `externalCustomerId`, `firstName`, `lastName`, and `channels` (email, and sanitized SMS phone number).
	* **Admin Deletion:** When contacts are deleted by an administrator, they are processed via the `DELETE` method with the parameter `erase => true` to ensure complete removal.
* **Bulk Method:** `POST /api/v1/contacts`
	* **Trigger:** Historical data is loaded automatically during the module installation.
	* **Batch Size:** 2000 contacts per request.
	* **Batch Execution:** The next batch is sent immediately after the previous one completes.
	* **Happy Flow:** Batches are accepted by the API, logging success.
* **Error Handling:** Any malformed data, API timeouts, or rejection responses trigger a log, ensuring no data loss goes unnoticed.

### 6. Order Synchronization (Real-time & Bulk)
* **Real-time Method:** `POST /api/v1/orders`
	* **Trigger:** OpenCart native event `checkout/order/addOrderHistory/after`.
	* **Payload:** Object containing `externalOrderId`, `externalCustomerId`, `totalCost`, mapped order status, and an `items` array (`externalItemId`, `name`, `cost`, `quantity`).
* **Bulk Method:** `POST /api/v1/orders` (Array Payload)
	* **Trigger:** Historical data is loaded automatically during the module installation.
	* **Batch Size:** 300 orders per request.
	* **Batch Execution:** The next batch is sent immediately after the previous one completes.
	* **Happy Flow:** Orders are mapped and accepted, triggering the success log.
* **Status Mapping:** Module translates OpenCart statuses (using `config_processing_status` and `config_complete_status`) to Yespo equivalents for accurate RFM analysis and trigger campaigns.
* **Error Handling:** Validation errors or API unavailability writing to system logs.

### 7. Background Logging System
* The module includes an isolated logging engine specifically for Yespo API interactions.
* It silently captures all connectivity issues, data validation errors, and file generation faults without exposing them to the frontend user.
* This provides developers with an actionable audit trail for debugging without affecting the store's conversion rates.

### 8. Data Mapping Reference

#### 8.1. Contact Field Mapping
The following table describes how OpenCart customer data is mapped to the Yespo API payload during contact synchronization:

| Yespo Payload Field | OpenCart Database Field | Transformation / Notes |
| :--- | :--- | :--- |
| `externalCustomerId`| `customer_id` | Integer. |
| `firstName` | `firstname` | |
| `lastName` | `lastname` | |
| `email` | `email` | |
| `phone` | `telephone` | Non-numeric characters are stripped |

#### 8.2. Order Field Mapping
The following table describes how OpenCart order data is mapped to the Yespo API payload during order synchronization:

| Yespo Payload Field | OpenCart Database Field | Transformation / Notes |
| :--- | :--- | :--- |
| `externalOrderId` | `order_id` | Integer. |
| `externalCustomerId`| `customer_id` | Included only if `customer_id > 0` |
| `totalCost` | `total` | Formatted according to OpenCart currency settings (without the currency symbol) |
| `date` | `date_added` | Converted to UTC ISO 8601 format (`Y-m-d\TH:i:s\Z`) |
| `currency` | Config `config_currency` | Currency code (e.g., USD, EUR) |
| `email` | `email` | |
| `phone` | `telephone` | Non-numeric characters are stripped |
| `firstName` | `firstname` | |
| `lastName` | `lastname` | |
| `deliveryMethod` | `shipping_method` | |
| `paymentMethod` | `payment_method` | |

#### 8.3. Order Items Mapping
Nested within the Order payload is the `items` array. Here is the mapping for individual products:

| Yespo Item Field | OpenCart Product Field | Transformation / Notes |
| :--- | :--- | :--- |
| `externalItemId` | `product_id` | Integer |
| `name` | `name` | |
| `quantity` | `quantity` | Integer |
| `cost` | `price` | Formatted according to OpenCart currency settings (without the currency symbol) |

#### 8.4. Order Status Mapping
OpenCart order statuses are dynamically mapped to Yespo statuses based on the store's global checkout settings.

| OpenCart Status Setting | Yespo Status | Condition |
| :--- | :--- | :--- |
| *None / Default* | `INITIALIZED` | Fallback status if the order does not match processing or complete statuses. |
| Processing Statuses (`config_processing_status`) | `IN_PROGRESS` | Applies if `order_status_id` matches any status in the processing array. |
| Complete Statuses (`config_complete_status`) | `DELIVERED` | Applies if `order_status_id` matches any status in the complete array. |

---

## 📋 Requirements

* **OpenCart:** 2.3.x or 3.0.x
* **PHP:** 5.6 or higher
* **Extension Installer:** Native OCMOD support enabled
* **Yespo Account:** Active Yespo CDP account with generated API credentials

---

## 🛠 Installation Guide

1. Download the latest `yespo.ocmod.zip` from the Releases page. **Important:** Only download releases directly from this official repository. Beware of pirate platforms and unofficial sources, which routinely deceive users and distribute modified, unsafe archives. Protect your store and customer data by avoiding such sites.
2. Log in to your OpenCart Admin Panel.
3. Navigate to **Extensions > Extension Installer**.
4. Click **Upload** and select the downloaded `yespo.ocmod.zip` file. Wait for the success message.
5. Go to **Extensions > Modifications** and click the **Refresh** button (top right corner) to rebuild the modifications cache.
6. Navigate to **Extensions > Extensions**, choose **Modules** from the dropdown list.
7. Find **Yespo CDP Integration** in the list and click the green **Install** button.
Details can be found here https://docs.yespo.io/docs/integration-with-opencart 

---

## 🚀 Configuration & Quick Start

Because of the automated architecture, configuration takes less than a minute:

1. In the Modules list, click **Edit** next to the Yespo CDP Integration module.
2. Paste your **Yespo API Key** into the designated field.
3. Click **Synchronize**.

**That’s it!** The module will instantly validate the key. Upon success, Web Tracking and Web Push will be live on your storefront, and the parallel background sync for existing Contacts and Orders will begin automatically.

---

## 🗑 Uninstallation

If you need to remove the module:
1. Go to **Extensions > Extensions > Modules** and click **Uninstall** for Yespo CDP Integration.
2. Go to **Extensions > Modifications**, select the Yespo modification, and click **Delete**, then click **Refresh**.

---

# Contributing to Yespo OpenCart Integration

We welcome contributions to the Yespo OpenCart Integration module! Whether you're fixing bugs, improving documentation, or adding new features, your contributions are appreciated.

## 🌿 Branch Structure

This project uses **Git Flow** workflow:
- **`main`** - Production-ready code, stable releases
- **`dev`** - Active development branch, all PRs should target this branch
- **Feature branches** - Created from `dev` for new features or fixes

## 🚀 Quick Start

1. **Fork the Repository**
   ```bash
   # Fork on GitHub, then clone your fork
   git clone git@github.com:ardas/yespo-opencart.git
   cd yespo-opencart
   # Switch to development branch
   git checkout dev
   ```

2. **Set Up Development Environment**
   Follow the Installation Guide in [README.md](readme.md) to install the module in your OpenCart development environment.

## 📝 Making Changes

### Branch Naming
- `feature/description` - for new features
- `fix/description` - for bug fixes
- `docs/description` - for documentation updates
- `refactor/description` - for code refactoring

### Commit Messages
Follow conventional commits:
```
type(scope): description

Example:
feat(sync): add support for new order status
fix(ui): resolve display issue in admin panel
docs(readme): update installation instructions
```

### Pull Request Process

1. **Create a Feature Branch from `dev`**
   ```bash
   git checkout dev
   git pull origin dev
   git checkout -b feature/your-feature-name
   ```

2. **Make Your Changes**
    - Follow our [coding standards](#-coding-standards)
    - Update documentation if needed

3. **Validate Your Changes**
    - Ensure your code follows OpenCart coding standards.
    - Test the module in your OpenCart installation (Admin and Catalog).
    - Ensure `install.xml` (OCMOD) works correctly and doesn't conflict with other extensions.

4. **Submit Pull Request to `dev`**
    - Push to your fork: `git push origin feature/your-feature-name`
    - Create a Pull Request **targeting the `dev` branch** with:
        - **Clear title** describing the change
        - **Detailed description** explaining:
            - What problem does this solve?
            - What changes were made?
            - How to test the changes?
        - **Screenshots** for UI changes
        - **Link to related issues**

## 🔧 Coding Standards

### OpenCart & PHP
- Follow the official **[OpenCart Coding Standards](http://docs.opencart.com/en-gb/developer/coding-standards/)**.
- Use **PHP 5.6+** compatible syntax (to maintain compatibility with older OpenCart versions).
- Follow OpenCart's MVC-L (Model-View-Controller-Language) architecture.
- Ensure all inputs are sanitized using OpenCart's `$this->db->escape()` or appropriate validation.
- Use OpenCart's built-in libraries (e.g., `$this->load->model()`, `$this->config->get()`).

### File Organization
The project follows the standard OpenCart structure:
```
src/
├── install.xml          # OCMOD modification file
└── upload/
    ├── admin/           # Admin panel files (Controller, Model, Language, View)
    ├── catalog/         # Frontend files (Controller, Model, Language, View)
    └── system/          # Library files and other system-level code
```

### Code Style
- Use **Tabs** for indentation (as per OpenCart standards).
- Follow **PSR-1** and **PSR-2** where it doesn't conflict with OpenCart standards.
- Use **meaningful names** for variables and functions.
- Add **proper error handling** and logging using OpenCart's `$this->log->write()`.

## 🐛 Bug Reports

Found a bug? Help us fix it by providing detailed information.

### Before Reporting
- Check if the issue already exists in GitHub Issues.
- Make sure you're using the latest version of the module.
- Try to reproduce the issue consistently.

### Bug Report Template
Use the GitHub bug report template when creating an issue. It should include steps to reproduce, environment details (OpenCart version, PHP version), and expected vs actual behavior.

## 💡 Feature Requests

Have an idea for improvement? We'd love to hear it!

### Feature Request Template
Use the GitHub feature request template when suggesting new features. Describe the problem it solves and how you envision the implementation.

## 🔒 Security

### Reporting Security Issues
**Do not report security vulnerabilities through public GitHub issues.**

Instead, please email us directly at: **support@yespo.io**

Include:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

### Security Guidelines for Contributors
- **Never commit** API keys, passwords, or secrets.
- Use **OpenCart configuration/database** for sensitive data, never hardcode.
- **Validate all inputs** and sanitize user data using OpenCart's security methods.
- Follow **OWASP security practices**.

## 🤝 Community Guidelines

### Code of Conduct
- **Be respectful** and inclusive.
- **Focus on constructive feedback**.
- **Help newcomers** feel welcome.
- **Assume good intentions**.
- **No harassment** or inappropriate behavior.

### Getting Help
- 📖 Check the [documentation](https://docs.yespo.io/docs/integration-with-opencart)
- 💬 Ask questions in GitHub Discussions
- 📧 Contact us at support@yespo.io
- 🐛 Report bugs through GitHub Issues

## 📊 Issue Management

### 🏷️ Labels We Use

| Label | Description | Used For |
|-------|-------------|----------|
| `bug` | Something isn't working | Bug reports |
| `feature` | New feature request | Feature requests |
| `enhancement` | Improvement to existing feature | Enhancements |
| `documentation` | Documentation needs update | Docs updates |
| `good first issue` | Good for newcomers | Beginner-friendly |
| `help wanted` | Extra attention needed | Community help |
| `question` | General questions | Q&A |
| `priority: critical` | Urgent fix needed | Critical bugs |
| `priority: high` | Should be fixed soon | Important issues |
| `priority: medium` | Normal priority | Standard issues |
| `priority: low` | Can wait | Minor issues |
| `status: waiting-for-feedback` | Needs more info | Pending response |
| `status: in-progress` | Being worked on | Active work |

---

Thank you for contributing to Yespo OpenCart Integration! 🙏

Your contributions help make Yespo better for everyone. Whether you're reporting bugs, suggesting features, or contributing code, every bit helps! 💙

---

## 📄 License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE.txt) file for details.