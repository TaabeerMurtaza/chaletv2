<!-- ## Single Chalet top details
- Rooms
- Guests
- Baths -->

# Chalet Edit Page – Field Reference

This is a comprehensive summary of all fields, their types, and options, as found in the Chalet edit page HTML (`tmp.html`). Use this as a reference to ensure all necessary fields are included in the redesign.

---

## Tabs/Sections
- **Information**
- **Tariffs**
- **Media**
- **Amenities**
- **Location**
- **Chambers**
- **Rules**
- **Timetable**

---

## 1. Information
- **Name of cottage** (`listing_title`): Text input. Free form. Required.
- **Description** (`description`): Rich text editor. Free form. Required.
- **Number of rooms** (`listing_bedrooms`): Text input (numeric). Free form. Required.
- **Number of guests** (`guests`): Number input. Free form. Required.
- **Number of beds** (`beds`): Text input (numeric). Free form. Required.
- **Number of bathrooms** (`baths`): Text input (numeric). Free form. Required.
- **Reservation link** (`affiliate_booking_link`): Text input (URL). Optional.
- **Featured** (`homey_featured`): Checkbox. Predefined: Yes/No. (Hidden input for value.)

---

## 2. Tariffs (Pricing)
- **Monthly rate from** (`night_price`): Text input (numeric). Free form. Required.
- **Household costs**:
    - **Cleaning fee** (`cleaning_fee`): Text input (numeric). Free form.
    - **Cleaning fee type** (`cleaning_fee_type`): Radio. Predefined: 'Monthly', 'By stay'.
- **Security deposit** (`security_deposit`): Text input (numeric). Free form. Optional.

---

## 3. Media
- **Gallery images** (`listing_image_ids[]`): Image upload. Multiple. Drag & drop, reorder, set featured.
- **Featured image** (`featured_image_id`): Hidden input, selected from uploaded images.

---

## 4. Amenities
- **Amenities**: Likely checkboxes or multi-select (not shown in detail in snippet). Predefined options.

---

## 5. Location
- **Address**: (Not fully shown in snippet, but present as a tab. Likely includes address fields, map, etc.)

---

## 6. Chambers (Bedrooms/Rooms)
- For each room (repeatable group):
    - **Name of room** (`homey_accomodation[n][acc_bedroom_name]`): Text input. Free form.
    - **Number of guests** (`homey_accomodation[n][acc_guests]`): Text input (numeric). Free form.
    - **Number of beds** (`homey_accomodation[n][acc_no_of_beds]`): Text input (numeric). Free form.
    - **Type of bed** (`homey_accomodation[n][acc_bedroom_type]`): Text input. Free form.
    - **Delete Room**: Button. Removes this room entry.
    - **Add a room**: Button. Adds a new room entry.

---

## 7. Rules
- **Cancellation policy** (`cancellation_policy`): Select. Predefined options:
    - Select the cancellation policy
    - Host policy
    - Flexible policy
    - Strict policy
    - Farm policy
- **Minimum number of months** (`min_book_months`): Text input (numeric). Free form.
- **Maximum number of months** (`max_book_months`): Text input (numeric). Free form.
- **Smoker allowed?** (`smoke`): Radio. Predefined: Yes/No.
- **Animals accepted?** (`pets`): Radio. Predefined: Yes/No.
- **Leaded party?** (`party`): Radio. Predefined: Yes/No.
- **Children allowed?** (`children`): Radio. Predefined: Yes/No.
- **Additional rules** (`additional_rules`): Rich text editor. Free form. Optional.

---

## 8. Timetable (Calendar)
- **Ical synchronization**: Import/Export calendar feeds (modal popups for inputting calendar name and URL).
- **Book a period**: Modal for selecting unavailable dates (not a field, but UI action).

---

## 9. Hidden/Meta Fields
- Various hidden fields for internal use: `draft_listing_id`, `homey_add_listing_nonce`, `action`, `post_author_id`, `current_tab`, `booking_type`, `listing_id`, etc. (Do not show in UI, but needed for form submission.)

---

## Field Types & Notes
- **Text input**: Free form, unless noted.
- **Number input**: Numeric only.
- **Select/Radio/Checkbox**: Predefined options.
- **Rich text editor**: Free form, supports formatting.
- **Image upload**: Multiple images, with drag & drop and featured selection.
- **Repeatable groups**: For rooms/chambers, user can add/remove entries.
- **Modals**: For calendar sync and period booking.

---

This list is exhaustive for all user-facing fields in the Chalet edit page as per the provided HTML. Use this as your checklist for the new design.