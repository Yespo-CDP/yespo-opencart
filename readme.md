# Yespo CDP Integration for OpenCart

The official integration module for connecting OpenCart 2.3.x and 3.0.x stores with [Yespo CDP](https://yespo.io/). 

Unlike manual setups, this module is built on a **"Zero-Configuration"** architecture. Once you provide your API key, the module autonomously handles validation, injects web tracking, web push, and initiates parallel synchronization of your contacts and orders.

---

## ⚙️ Implementation Details & How It Works

This module is designed to work silently and efficiently in the background without requiring complex field mapping or manual toggles. Here is the full breakdown of the technical implementation:

### 1. API Key Validation & Initialization
Upon entering the Yespo API Key in the module settings, the system instantly sends a test request to the Yespo API. If validated successfully, the module automatically activates the following three core processes in parallel.

### 2. Automated Web Tracking
The module automatically integrates Yespo's behavioral tracking into your storefront.
* **Script Injection:** The tracking snippet is dynamically injected into the `<head>` or before the `</body>` tag of all storefront pages via OpenCart events (no core file modifications required).
* **Behavioral Events:** It tracks user browsing history, cart additions, and purchases, allowing you to trigger "Abandoned Cart" or "Browse Abandonment" workflows directly from Yespo.

### 2. Automated Web Push
The module automatically integrates Yespo's Web Push script and Service Worker your storefront.
* **Script and Service Worker Injection:** The web push script is dynamically injected into the `<head>` or before the `</body>` tag of all storefront pages via OpenCart modification system.

### 3. Contact Synchronization
Customer data is pushed to Yespo seamlessly:
* **Real-time Sync:** Hooked into OpenCart's native `customer/add` and `customer/edit` events. Whenever a user registers or updates their profile, the payload is immediately pushed to Yespo.
* **Historical Import:** Runs a background script that pulls existing customers using batched queries (`LIMIT/OFFSET`) to prevent memory exhaustion on large databases.
* **Mapped Data:** Automatically maps OpenCart fields (First Name, Last Name, Email, Phone) to Yespo's standard contact schema.

### 4. Order Synchronization
Sales data flows into Yespo for RFM analysis and post-purchase campaigns:
* **Real-time Sync:** Triggered via the `checkout/order/addOrderHistory` event. New orders and status changes are synced instantly.
* **Historical Import:** Syncs past orders in batches, similarly to contacts.
* **Order Payload:** Includes Order ID, Customer ID (if registered) or Guest Email/Phone, Items array (Product ID, Name, Price, Quantity), Total Value, and Currency.
---

## 📋 Requirements

* **OpenCart:** 2.3.x or 3.0.x
* **PHP:** 5.6 or higher
* **Extension Installer:** Native OCMOD support enabled
* **Yespo Account:** Active Yespo CDP account with generated API credentials

---

## 🛠 Installation Guide

1. Download the latest `yespo.ocmod.zip` from the Releases page.
2. Log in to your OpenCart Admin Panel.
3. Navigate to **Extensions > Extension Installer**.
4. Click **Upload** and select the downloaded `yespo.ocmod.zip` file. Wait for the success message.
5. Go to **Extensions > Modifications** and click the **Refresh** button (top right corner) to rebuild the cache and apply event hooks.
6. Navigate to **Extensions > Extensions**, choose **Modules** from the dropdown list.
7. Find **Yespo CDP Integration** in the list and click the green **Install** button.

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

## 📄 License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.