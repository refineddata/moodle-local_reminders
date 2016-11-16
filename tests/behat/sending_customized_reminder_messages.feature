@refined_training @local_reminders @local @sending_customized_reminder_messages
  Feature: sending_customized_reminder_messages
    In order to receive customized reminder messages
    As an admin
    I need to add a customized reminder message

  Background:
    Given I log in as "admin"
    And I am on homepage
    When I follow "Turn editing on"
    And I add the "RefinedTools" block
    Then I should see "RefinedTools"
    And I am on homepage
    And I follow "Browse Reminders"
    And I follow "Add reminder"
    And I set the following fields to these values:
     | Message ID | iEvent Reminder |
     | Subject    | Welcome to [[name]] |
     | From Name  | Melissa Gold <melissa@refineddata.com> |
     | Body       | Dear [[firstname]], Your event [[name]] will take place on [[date]] [[timezone]]. It will last [[mins]]. Just click here to go to your course [[course]]. |
    And I press "Save changes"
    And I follow "Add reminder"
    And I set the following fields to these values:
     | Message ID | Basic Meeting Reminder |
     | Language   | English (en)           |
     | Subject    | Welcome to your event, [[name]] |
     | From Name  | Melissa Gold <melissa@refineddata.com> |
     | Body       | Dear [[firstname]], Your event, [[name]], is set to take place [[date]] [[timezone]]. It will last [[duration]]. To enter your meeting, just click [[meeting]]. The RT Team |
    And I press "Save changes"
    And I log out


  @javascript
  Scenario: Log in as admin to check if the reminders are available
    Given I log in as "admin"
    And I am on homepage
    Then I follow "Course 1"
    Then I follow "Browse Reminders"
    Then I should see "iEvent Reminder"
    Then I should see "Basic Meeting Reminder"
    And I log out




