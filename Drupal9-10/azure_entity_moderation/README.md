# Azure Entity Moderation Module

## Description

The Azure Entity Moderation module is crafted for Drupal 9 and Drupal 10, offering seamless entity moderation through the Azure Text Analytics API. This module retrieves sentiment analysis results for specified entity fields and presents the findings using three distinct formatters:

1. **Number:** Displaying sentiment analysis results as a numerical value.
2. **Color Representation:** Visualizing sentiment analysis results through color representation.
3. **Text Representation:** Displaying sentiment analysis results as text.

**Note:** Before deploying this module, ensure the dependency module has been extended with the patch provided in this issue: [Azure Text Analytics API Patch](https://www.drupal.org/project/azure_text_analytics_api/issues/2972646).

## Installation Steps

1. **Enable the Module:**
   - Install and enable the module using the standard Drupal procedures.

2. **Add Azure Entity Moderation Element to Webform:**
   - Once the module is enabled, integrate the Azure Entity Moderation element into your webform.

3. **Configure Webform Settings:**
   - Visit the webform settings tab.
   - Add a new handler named "Azure Entity Moderation Handler."

4. **Configure Entity Moderation Handler:**
   - In the Azure Entity Moderation Handler settings, configure the following:
     - Select entity fields for sentiment analysis.
     - Choose the desired formatter for displaying analysis results (Number, Color Representation, or Text Representation).

5. **Save Configuration:**
   - Preserve your webform configuration after adding and configuring the Azure Entity Moderation Handler.

## Usage

- Following the installation steps, the Azure Entity Moderation module will automatically procure sentiment analysis results for the designated entity fields and exhibit them according to the chosen formatter.

- Users can observe sentiment analysis results as a numerical value, color representation, or text representation, based on the configured settings.

## Contribution

- If you encounter issues or have improvement suggestions, please report them on the [module's issue queue](https://www.drupal.org/project/issues/azure_entity_moderation).

- Contributions and patches are welcome. Feel free to submit pull requests to the module's [GitHub repository](https://github.com/yourusername/azure_entity_moderation).

## License

This module adheres to the [GNU General Public License, version 2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

*This README.md file aligns with Drupal 9/10 coding standards and provides essential information about the Azure Entity Moderation module, including installation steps, usage instructions, and contribution details. Refer to the [module's documentation](https://www.drupal.org/project/azure_entity_moderation) for more detailed information.*

