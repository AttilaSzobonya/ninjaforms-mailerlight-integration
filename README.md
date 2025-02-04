# 🚧 NinjaForms MailerLite Integration 🚧

![GitHub](https://img.shields.io/badge/status-under%20development-red)
![GitHub](https://img.shields.io/github/license/AttilaSzobonya/ninjaforms-mailerlite-integration)
![GitHub stars](https://img.shields.io/github/stars/AttilaSzobonya/ninjaforms-mailerlite-integration?style=social)

A custom NinjaForms action to integrate with MailerLite, allowing you to map form fields to MailerLite fields dynamically. 📋➡️📧

---

## ⚠️ **WARNING: UNDER DEVELOPMENT** ⚠️

This project is **not ready for production use**. It is still in the early stages of development, and there may be bugs, missing features, or breaking changes. Use at your own risk! 🛑

If you'd like to contribute, feel free to open an issue or submit a pull request. 🙌

---

## 📖 Overview

This snippet adds a custom action to NinjaForms that allows you to send form submissions to MailerLite. You can map any form field to any MailerLite field (e.g., email, name, custom fields) for maximum flexibility. 🎯

### Key Features:
- **Dynamic Field Mapping**: Map any form field to any MailerLite field. 🔄
- **Group Support**: Optionally add subscribers to a specific MailerLite group. 🗂️
- **Resubscribe Option**: Automatically resubscribe unsubscribed users. 🔄📧

---

## 🛠️ Installation

### Using Code Snippets Plugin

1. **Install the Code Snippets Plugin**:
   - If you don't already have it, install and activate the [Code Snippets](https://wordpress.org/plugins/code-snippets/) plugin from the WordPress repository.

2. **Add the Snippet**:
   - Go to **Snippets → Add New** in your WordPress admin dashboard.
   - Give the snippet a title (e.g., "NinjaForms MailerLite Integration").
   - Copy and paste the code from this repository into the code area.
   - Set the **Code Type** to **PHP**.
   - Save and activate the snippet.

3. **Configure in NinjaForms**:
   - Go to NinjaForms → Emails & Actions.
   - Add the "MailerLite Integration" action to your form.
   - Enter your MailerLite API key and configure field mappings.

---

## 🧰 Usage

### 1. **MailerLite API Key**
   - Obtain your API key from your MailerLite account.
   - Add it to the action settings in NinjaForms.

### 2. **Field Mappings**
   - Use the "Field Mappings" repeater to map your form fields to MailerLite fields.
   - Example:
     - Form Field: `email` → MailerLite Field: `email`
     - Form Field: `first_name` → MailerLite Field: `name`
     - Form Field: `phone` → MailerLite Field: `custom_field`

### 3. **Group ID (Optional)**
   - If you want to add subscribers to a specific group, enter the MailerLite Group ID.

---

## 🚀 Roadmap

- [ ] Add support for custom fields in MailerLite.
- [ ] Integrate into a standalone WordPress plugin.
- [ ] Improve error handling and logging.
- [ ] Add unit tests.
- [ ] Create a settings page for global configuration.
- [ ] Submit to the WordPress Plugin Repository.

---

## 🤝 Contributing

Contributions are welcome! If you'd like to contribute, please:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Submit a pull request.

Please ensure your code follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/).

---

## 📜 License

This project is licensed under the *MIT License**. See the [LICENSE](LICENSE) file for details.

---

## 🔗 Links

- [NinjaForms](https://ninjaforms.com/)
- [MailerLite](https://www.mailerlite.com/)
- [Code Snippets Plugin](https://wordpress.org/plugins/code-snippets/)
- [GitHub Repository](https://github.com/yourusername/ninjaforms-mailerlite-integration)

---

## 🙏 Credits

- Developed by [Attila Szobonya](https://github.com/AttilaSzobonya).
- Inspired by the need for a missing MailerLite integration with NinjaForms.

**Happy Coding!** 🎉