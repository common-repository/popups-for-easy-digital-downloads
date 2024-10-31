=== Popups for Easy Digital Downloads ===
Tags: edd, popups, easy digital downlads
Contributors: aspengrovestudios, annaqq
Tested up to: 6.6.1
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Contributors: aspengrovestudios
Easy Digital Downloads integration for WP Popups


== Description ==

Easy Digital Downloads integration for WP Popups

## Display Rules

The plugin adds the following Display Rules to be used on WP Popups forms.

### User has purchased product

Evaluates to `true` if the logged-in user has ever purchased the specified product, as determined by Easy Digital Downloads.

### User has product in cart

Evaluates to `true` if the logged-in user currently has the specified product in their cart, as determined by Easy Digital Downloads.


### User cart product count

Performs a numeric comparison against the count of the current cart contents.

### User purchase count

Performs a numeric comparison against the number of past purchases by the current user, as reported by Easy Digital Downloads.

### User purchase total

Performs a numeric comparison against the total amount of past purchases by the current user, as reported by Easy Digital Downloads.

## Triggers

The plugin adds the following Triggers to be used on WP Popups forms (Settings > Triggers).

### Easy Digital Downloads - before add to cart

Listens for clicks on add to cart buttons using the same criteria as the edd-ajax script (part of Easy Digital Downloads) uses to initiate an ajax add to cart action. When this trigger is enabled, the popup is shown when the add to cart button is clicked, and the add to cart process is interrupted. If a [conversion event](https://wppopups.com/docs/how-to-create-a-custom-conversion-link/) occurs in the popup, the add to cart process will proceed. If the popup should include a link to close the popup without adding the product to cart, see [this article](https://wppopups.com/docs/how-to-add-a-custom-close-button/) for instructions.

### Easy Digital Downloads - added to cart

Listens for EDD's "cart item added" JavaScript event (indicating a successful cart addition via ajax) and triggers the popup. This trigger does not interrupt any processes.