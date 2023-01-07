# Nox Framework

The purpose of this framework is to provide a modular foundation that supports integration with any
game servers on any platform.

It's still very, very, early days, and currently the focus is on getting a working PoC (proof-of-concept) for people to play with (they're a visual client and thus requested a usable demo to play with). 

### Summary of features so far:
- Module support (install custom systems, including custom integrations for games)
- Theme support (serve a personalised theme for your game, with support for parent themes)
- Automatic updates (update checks are scheduled, and ability to auto-update the framework - and dependencies - via Composer at just a click of a button)
- Support for roles and permissions (with ability to customise roles and role access)
- Discord authentication (since Discord is the industry standard form of supporting a community, basic auth has been disabled and replaced only with Discord oauth)
- Configure the site without updating the code (also includes an installer to quickly get you up and running)
- Per-user locale (change app locale depending on user choice/location. Base support for translations)
- Persistent settings (for data that's not appropriate or sensitive for the .env, but shouldn't be cleared with the Cache. Follows similar API to the Cache facade)

### TODO
- Tests! Currently most of my time is spent getting a proof-of-concept running, I am desperate to go back and add feature tests for everything! (once I have finalised the API's)
- Add model policies for default models (roles, users, etc)
- Clean up installer and Settings page code (a lot of overlapping code)
- Support updating Modules/Themes via Composer
- Ensure I haven't missed any hard-coded strings during my PoC phase and replace with translations
- Add support for an AJAX API that's not dependant on Livewire (eventually separating from a monolith to separate front-end/back-end and eventually mobile app)
- Perhaps renaming from `nox-php/framework`
