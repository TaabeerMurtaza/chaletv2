# Informations
- chalet name (post title)
- description (carbon field)
- guest count
- baths
- bedrooms[]
- - name
- - guests
- - beds
- - type


# Price
## Default Rates (only numbers)
- weekend price per night (friday, saturday)
- weekdays price per night (sunday, monday, tuesday, wednesday, thursday, ) 
- price per week (7 days)
## Extra guests
- guests included (integer)
- extra price per guest per night (18+)
- extra price per child per night (3-17)
- free for babies (checkbox, checked => free for babies, unchecked => child rates per baby)
## Miminum nights of booking
- minimum nights of booking
## Taxes
- GST tax number
- THQ tax number (Mandatory)
- QST tax number
- Add your CITQ document (file)
## Security Deposit
- security deposit (number)
## Extra options
- Extra options[]
- - name
- - price
- - type of fee (dropdown => Fees per stay, Fees per number of items)
## Checkin/Checkout Extras
- checkin time (dropdown => select the time of regular checkin)
- checkout time (dropdown => select the time of regular checkout)
- early checkin[]
- - time (dropdown => select the time of early checkin)
- - price
- late checkout[]
- - time (dropdown => select the time of late checkout)
- - price
## Seasonal rates (These prices will override default rates)
- Seasonal rates[]
- - period[]
- - - start date
- - - end date
- - - price per night
- - - price for saturday
- - - price for sunday
- - - price for monday
- - - price for tuesday
- - - price for wednesday
- - - price for thursday
- - - price for friday
- - - ### Fee for additional guests (just a new section in same sequence)
- - - charge after # guests (integer) (these additional charges will apply after exceeding this number of guests)
- - - extra price per guest per night (18+)
- - - extra price per child per night (3-17)
- - - extra price per baby per night (0-2)
- - - minimum length of stay (integer, nights)
- - - checkin unavailable[] => (saturday, sunday, monday, tuesday, wednesday, thursday, friday) (checkboxes) [Confirm_what_these_are_for]
- - - checkout unavailable[] => (saturday, sunday, monday, tuesday, wednesday, thursday, friday) (checkboxes) [Confirm_what_these_are_for]
- - - early checkin unavailable[] => (saturday, sunday, monday, tuesday, wednesday, thursday, friday) (checkboxes) 
- - - late checkout unavailable[] => (saturday, sunday, monday, tuesday, wednesday, thursday, friday) (checkboxes) 


# Terms
- reservation policies (radio) (Policies 50-50 (3 days before stay), Policies 50-50 (14 days before stay), Policies 25-25-50 (14 days before stay))
- cancellation policies (radio) (Flexible, Moderate, Strict)
- preparation time (number of nights blocked before the start of the stay)
- reservation window (number of nights in advance a user can book)
- reservation notice (Minimum number of days from arrival date to accept a reservation, basically minimum notice days)
- reservation contract (freeform, like policies n stuff)

# Instructions
- checkin instructions
- checkin instructions days (how many days before checkin these instructions need to be provided)
- checkout instructions
- checkout instructions days (how many days before checkout these instructions need to be provided)
- itinerary instructions
- itinerary instructions days (how many days before checkin these instructions need to be provided)
- reminder of rules
- reminder of rules days (how many days before checkin these instructions need to be provided)
- local guide
- local guide days (how many days before checkin these instructions need to be provided)
- emergency contact

# Images
- images[]
- video link
# Amenities
- indoor features (selected from a cpt chalet_feature where carbon meta 'feature_type' = 'indoor')
- outdoor features (selected from a cpt chalet_feature where carbon meta 'feature_type' = 'outdoor')
- kitchen features (selected from a cpt chalet_feature where carbon meta 'feature_type' = 'kitchen')
- family features (selected from a cpt chalet_feature where carbon meta 'feature_type' = 'family')
- sports features (selected from a cpt chalet_feature where carbon meta 'feature_type' = 'sports')
- services features (selected from a cpt chalet_feature where carbon meta 'feature_type' = 'services')
- accessibility features (selected from a cpt chalet_feature where carbon meta 'feature_type' = 'accessibility')
- events features (selected from a cpt chalet_feature where carbon meta 'feature_type' = 'events')
# Location
- full address
- country
- province
- region
- longitude (hidden field, auto added by placing pin on google maps)
- latitude (hidden field, auto added by placing pin on google maps)
# Calendar (available dates etc using calendar plugin)