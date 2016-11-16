@refined_training @local_reminders @local @local_addreminders
  Feature: local_addreminders
    In order to get the reminders
    As an admin
    I need to add a reminder

  Background:
    Given I log in as "admin"
    Then I press "Add a new course"
    And I set the following fields to these values:
     | Course full name | Course 1 |
     | Course short name | C1      |
     | Course category   | Miscellaneous |
    And I press "Save changes"
    And I press "Return to course"
    And I follow "Turn editing on"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    And I press "Cancel"
    And I follow "Browse Reminders"
    Then I follow "Add reminder"
    And I set the following fields to these values:
     | Message ID | Basic Meeting Reminder |
     | Subject    | Welcome to your event, [[name]] |
     | From Name  | Melissa Gold <melissa@refineddata.com> |
     | Body       | Dear [[firstname]], Your event [[name]], is set to take place [[date]] [[timezone]]. It will last [[duration]] |
    And I press "Save changes"
    Then I follow "Add reminder"
    And I set the following fields to these values:
     | Message ID | Certificate expiry message |
     | Subject    | Your certificate in [[course]] is about to expire |
     | From Name  | Melissa Gold <melissa@refineddata.com>            |
     | Body       | Hello [[firstname]], Tour certificate in [[course]] will expire shortly. To update your training and acquire a current certificate, visit and retake the course |
    And I press "Save changes"
    Then I follow "Add reminder"
    And I set the following fields to these values:
     | Message ID | iEvent Reminder |
     | Subject    | Welcome to [[name]] |
     | From Name  | Melissa Gold <melissa@refineddata.com> |
     | Body       | Dear [[firstname]], Your event [[name]] will take place on [[date]] [[timezone]]. It will last [[mins]]. Just click here to go to your course [[course]]. |
    And I press "Save changes"
    Then I log out


  @javascript
  Scenario: Check if the meeting is added in the course
    Given I log in as "admin"
    And I am on homepage
    Then I follow "Course 1"
    Then I follow "Browse Reminders"
    Then I should see "Basic Meeting Reminder"
    Then I should see "Certificate expiry message"
    Then I should see "iEvent Reminder"
    Then I log out

