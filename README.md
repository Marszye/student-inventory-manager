# 📦 Student Lending Engine: Boarding School Inventory System

**Student Lending Engine** is a robust management platform designed to automate the process of borrowing institutional items (such as books, electronics, or sports equipment) within a boarding school environment. The system ensures that every item is accounted for, reducing asset loss and streamlining the administrative burden on staff.

---

## 🚀 Key Features
- **Student-Centric Dashboard:** Simple interface for students to browse available items and request a loan.
- **Stock Management Logic:** Real-time inventory tracking that automatically updates as items are borrowed or returned.
- **Borrowing Timeline:** Automated logging of "Borrow Date" and "Expected Return Date" to maintain accountability.
- **Overdue Alert System:** Flags students who have exceeded their borrowing limit or failed to return items on time.
- **Verification Gate:** Admin-side approval to verify the item condition before and after the lending process.

---

## 🛠️ Tech Stack
- **Backend:** Native PHP (PDO) — Optimized for high-frequency transaction logging.
- **Database:** MySQL — Relational structure connecting Students, Items, and Transactions.
- **Frontend:** Tailwind CSS — Clean, intuitive, and mobile-friendly for on-the-go inspections.
- **Validation:** Server-side logic to prevent double-borrowing of the same unique asset.

---

## ⚙️ How it Works
1. **Request:** A student selects an item from the available inventory list.
2. **Approval:** The supervisor verifies the request and releases the item, updating the status to `On Loan`.
3. **Tracking:** The system monitors the duration. If it passes the deadline, the item is highlighted in the "Overdue" section.
4. **Return:** Upon return, the admin inspects the item condition and marks the transaction as `Complete`, returning the item to the active inventory.

---

## 🛡️ Strategic Value
This project demonstrates the ability to manage **Resource Allocation** and **Asset Protection**. By digitizing the borrowing process, schools can maintain a high level of discipline regarding shared resources while providing students with easy access to the tools they need.
