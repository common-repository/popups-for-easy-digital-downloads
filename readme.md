# Easy Digital Downloads integration for WP Popups

## Display Rules

The plugin adds the following Display Rules to be used on WP Popups forms.

### User has purchased product

Evaluates to `true` if the logged-in user has ever purchased the specified product, as determined by Easy Digital Downloads.

### User has product in cart

Evaluates to `true` if the logged-in user currently has the specified product in their cart, as determined by Easy Digital Downloads.

### User has active license key for product

Evaluates to `true` if the logged-in user has a license key for the specified product with either "active" or "inactive" status (i.e. not expired, disabled, etc.), as determined by Easy Digital Downloads Software Licensing.

### User does not have active license key for product

Evaluates to `true` if the logged-in user does not have a license key for the specified product with either "active" or "inactive" status (i.e. not expired, disabled, etc.), as determined by Easy Digital Downloads Software Licensing.

### User cart product count

Performs a numeric comparison against the count of the current cart contents.

### User has license key(s) expiring within (days)

Retrieves all license keys belonging to the current user (as determined by Easy Digital Downloads Software Licensing), and checks if any of the keys with "active" or "inactive" status (i.e. not expired, disabled, etc.) have an expiration timestamp value that, when the current timestamp is subtracted from it, results in a difference (in days) that satisfies the specified numeric condition.

### User purchase count

Performs a numeric comparison against the number of past purchases by the current user, as reported by Easy Digital Downloads.

### User purchase total

Performs a numeric comparison against the total amount of past purchases by the current user, as reported by Easy Digital Downloads.

## Triggers

The plugin adds the following Triggers to be used on WP Popups forms (Settings > Triggers).

### Easy Digital Downloads - before subscription cancellation

Listens for clicks on the subscription cancellation link from Easy Digital Downloads - Recurring Payments (class `edd_subscription_cancel`). When this trigger is enabled, the popup is shown when the link is clicked, and the subscription cancellation process is interrupted. If a [conversion event](https://wppopups.com/docs/how-to-create-a-custom-conversion-link/) occurs in the popup, the subscription cancellation will proceed. If the popup should include a link to close the popup without cancelling the subscription, see [this article](https://wppopups.com/docs/how-to-add-a-custom-close-button/) for instructions.

### Easy Digital Downloads - before add to cart

Listens for clicks on add to cart buttons using the same criteria as the edd-ajax script (part of Easy Digital Downloads) uses to initiate an ajax add to cart action. When this trigger is enabled, the popup is shown when the add to cart button is clicked, and the add to cart process is interrupted. If a [conversion event](https://wppopups.com/docs/how-to-create-a-custom-conversion-link/) occurs in the popup, the add to cart process will proceed. If the popup should include a link to close the popup without adding the product to cart, see [this article](https://wppopups.com/docs/how-to-add-a-custom-close-button/) for instructions.

### Easy Digital Downloads - added to cart

Listens for EDD's "cart item added" JavaScript event (indicating a successful cart addition via ajax) and triggers the popup. This trigger does not interrupt any processes.
