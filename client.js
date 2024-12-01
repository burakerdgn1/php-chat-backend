const fetch = require('node-fetch');
const readline = require('readline');

// Backend API URLs
const BASE_URL = 'http://localhost:8000';

// Console Input Setup
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
});

// User session
let username;
let currentGroupId;

// Utility to prompt the user for input
const prompt = (query) => new Promise((resolve) => rl.question(query, resolve));


// Fetch and display the list of groups
const fetchGroups = async () => {
    try {
        const response = await fetch(`${BASE_URL}/groups`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' },
        });

        if (!response.ok) {
            console.error('Failed to fetch groups:', await response.json());
            return [];
        }

        const groups = await response.json();
        console.log('Available Groups:');
        groups.forEach((group, index) => {
            console.log(`${index + 1}. Group ID: ${group.id}, Name: ${group.name}`);
        });

        return groups;
    } catch (error) {
        console.error('Error fetching groups:', error.message);
        return [];
    }
};

// Join a group
const joinGroup = async (groupId) => {
    try {
        const response = await fetch(`${BASE_URL}/groups/${groupId}/join`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: username }),
        });

        if (!response.ok) {
            console.error('Failed to join group:', await response.json());
            return false;
        }

        console.log(`Successfully joined Group ID: ${groupId}`);
        return true;
    } catch (error) {
        console.error('Error joining group:', error.message);
        return false;
    }
};


const pollMessages = async () => {
    if (!currentGroupId || !username) return;

    try {
        const response = await fetch(`${BASE_URL}/messages/${currentGroupId}/${username}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' },
        });

        if (!response.ok) {
            console.error('Failed to fetch messages:', await response.json());
            return;
        }

        const messages = await response.json();

        // Save the current input line
        const currentLine = rl.line;

        // Clear the console and display the messages
        console.clear();
        console.log('--- Group Messages ---');
        messages.reverse().forEach((msg) => {
            console.log(`[${msg.created_at}] User ${msg.user_id}: ${msg.message}`);
        });
        console.log('----------------------');

        // Restore the input line
        rl.write(null, { ctrl: true, name: 'u' }); // Clears the current input
        rl.write(currentLine); // Writes back the saved input
    } catch (error) {
        console.error('Error fetching messages:', error.message);
    }
};


// Post a new message
const postMessage = async (messageContent) => {
    if (!currentGroupId) {
        console.error('You need to join a group first!');
        return;
    }

    try {
        const response = await fetch(`${BASE_URL}/messages/${currentGroupId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                user_id: username,
                message: messageContent,
            }),
        });

        if (!response.ok) {
            console.error('Failed to post message:', await response.json());
        } else {
            console.log('Message posted successfully.');
        }
    } catch (error) {
        console.error('Error posting message:', error.message);
    }
};

const checkUsernameAndFetchGroups = async (username) => {
    try {
        const response = await fetch(`${BASE_URL}/groups/user-groups/${username}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        if (!response.ok) {
            console.error('Failed to validate username:', await response.json());
            return null;
        }

        const { groups } = await response.json();
        return groups; // Returns an array of groups or an empty array if the user is new
    } catch (error) {
        console.error('Error checking username:', error.message);
        return null;
    }
};

// Select one of your groups to chat in
const selectOwnGroup = async (ownGroups) => {
    console.log('Your Groups:');
    ownGroups.forEach((group, index) => {
        console.log(`${index + 1}. Group ID: ${group.id}, Name: ${group.name}`);
    });

    const groupChoice = await prompt('Enter the number of the group to select: ');
    const groupIndex = parseInt(groupChoice, 10) - 1;

    if (ownGroups[groupIndex]) {
        currentGroupId = ownGroups[groupIndex].id;
        console.log(`Switched to Group ID: ${currentGroupId}`);
        return true;
    } else {
        console.log('Invalid group selection.');
        return false;
    }
};

// Main function
const main = async () => {
    console.log('Welcome to the Chat Client!');

    while (true) {
        username = await prompt('Enter your Username: ');

        if (!username.trim()) {
            console.log('Username cannot be empty or just spaces. Please try again.');
            continue;
        }

        const ownGroups = await checkUsernameAndFetchGroups(username);

        if (ownGroups === null) {
            console.log('Error occurred while checking username. Try again later.');
            continue;
        }

        if (ownGroups.length > 0) {
            console.log(`Welcome back, ${username}! You belong to the following groups:`);
            ownGroups.forEach((group, index) => {
                console.log(`${index + 1}. Group ID: ${group.id}, Name: ${group.name}`);
            });
        } else {
            console.log(`Welcome, ${username}! It looks like you're new here.`);
            console.log("Let's join a group to start chatting.");
        }

        break; // Exit the loop once the username is validated
    }

    let pollingInterval;

    while (true) {
        // Display options menu once per loop iteration
        console.log('\nOptions:');
        console.log('1. View and Join a Group');
        console.log('2. Select one of your groups to chat in');
        console.log('3. Chat in Current Group');
        console.log('4. Exit');

        const option = await prompt('Choose an option: ');

        switch (option) {
            case '1': // View and Join a Group
                const groups = await fetchGroups();
                if (groups.length === 0) break;

                const groupChoice = await prompt('Enter the number of the group to join: ');
                const groupIndex = parseInt(groupChoice, 10) - 1;

                if (groups[groupIndex]) {
                    const success = await joinGroup(groups[groupIndex].id);
                    if (success) {
                        currentGroupId = groups[groupIndex].id;
                        console.log(`Joined Group ID: ${currentGroupId}`);

                        // Clear any previous polling interval
                        if (pollingInterval) clearInterval(pollingInterval);

                        // Start polling messages for the new group
                        //pollingInterval = setInterval(pollMessages, 5000);
                    }
                } else {
                    console.log('Invalid group selection.');
                }
                break;

            case '2': // Select one of your groups to chat in
                const ownGroups = await checkUsernameAndFetchGroups(username);
                if (ownGroups.length === 0) {
                    console.log('You do not belong to any groups. Join a group first.');
                    break;
                }
                await selectOwnGroup(ownGroups);
                break;


            /*const switched = await selectOwnGroup(ownGroups);
            if (switched && pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = setInterval(pollMessages, 5000);
            }
            break;*/

            case '3': // Chat in Current Group
                if (!currentGroupId) {
                    console.log('You need to join a group first.');
                    break;
                }

                console.log('Start chatting! (type "exit" to stop)');

                messagePolling = setInterval(pollMessages, 5000);
                while (true) {
                    const message = await prompt('> ');
                    if (message.toLowerCase() === 'exit') break;
                    await postMessage(message);
                }

                // Stop polling when user exits chat
                clearInterval(messagePolling);
                console.log('Stopped chatting in the current group.');
                break;

            case '4': // Exit
                console.log('Goodbye!');
                rl.close();

                // Clear polling interval before exiting
                if (pollingInterval) clearInterval(pollingInterval);

                process.exit(0);

            default:
                console.log('Invalid option. Please try again.');
        }
    }
};

// Run the client
main();
