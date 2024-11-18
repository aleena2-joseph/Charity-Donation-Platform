Charity Donation Tracking System
The Charity Donation Tracking System is a decentralized application (DApp) that allows transparent and secure charity donations using Ethereum smart contracts. The system is built with Hardhat for blockchain interaction and PHP for the frontend, enabling donors to contribute to charity causes in a seamless and traceable manner.

Getting Started
Follow these steps to set up the Charity Donation Tracking System locally, which includes deploying the smart contract and running the frontend PHP server.

Prerequisites
Ensure the following tools are installed on your machine:

Node.js (v16 or later)
npm (v8 or later)
PHP (for frontend)
Composer (for managing PHP dependencies, if necessary)
Step 1: Clone the Repository
Start by cloning the repository to your local machine:

bash
Copy code
git clone https://github.com/your-username/kba-project-main.git
Navigate to the project directory:

bash
Copy code
cd kba-project-main
Step 2: Install Dependencies
Install Smart Contract Dependencies (Hardhat)
Navigate to the hardhat directory and install all the required dependencies for the smart contract:

bash
Copy code
cd hardhat
npm install
This will install Hardhat, ethers.js, and other dependencies necessary for the smart contract development.

Install Frontend Dependencies (PHP)
For the frontend, if you are using any PHP dependencies or frameworks (like Laravel, Symfony, etc.), you'll need to install them using Composer. Navigate to the frontend directory and run:

bash
Copy code
cd ../frontend
composer install
This will install any required PHP dependencies. If you don't use Composer or PHP frameworks, you can skip this step.

Step 3: Compile Smart Contracts
Once the dependencies are installed, it's time to compile the smart contracts. Go back to the hardhat directory and run:

bash
Copy code
npx hardhat compile
This will compile your Solidity contracts into artifacts, which are necessary for deployment.

Step 4: Deploy the Smart Contracts
To deploy the smart contracts, you need to first start a Hardhat network to simulate the blockchain locally:

bash
Copy code
npx hardhat node
This will start a local Ethereum network on http://localhost:8545. Keep this terminal running as the local Ethereum node.

Now, open another terminal window and deploy the contracts to this local network:

bash
Copy code
npx hardhat run scripts/deploy.js --network localhost
This will deploy your smart contracts to the local Ethereum network. After successful deployment, you will see the deployed contract's address. Keep this address, as you’ll need it in the next step to configure the frontend.

Step 5: Configure Frontend
After deploying the smart contract, you need to update the frontend to interact with the deployed smart contract.

Open the frontend PHP files where the contract address is referenced.
Paste the contract address from the deployment step into the appropriate location in the frontend code.
You may need to configure ethers.js or another web3 provider in your PHP files to interact with the deployed smart contract.
For PHP, you can use the ethers.js JavaScript library to communicate with the Ethereum network from the frontend.

Step 6: Run the PHP Server
Once the contract is deployed and the frontend is configured, you need to run the PHP server.

If you're using PHP's built-in server, navigate to the frontend directory and run:

bash
Copy code
cd frontend
php -S localhost:8000
This will start a local server at http://localhost:8000, where you can access the frontend.

Alternatively, if you are using a tool like XAMPP or WAMP, ensure that the htdocs or equivalent directory is set to point to the frontend directory, and start the server using the XAMPP/WAMP control panel.

Step 7: Access the Application
Now that everything is set up, open your browser and visit:

plaintext
Copy code
http://localhost:8000
You should see the Charity Donation Tracking System frontend, where users can donate, register, and track charity transactions.

Technologies Used
Hardhat: Ethereum development environment for smart contract compilation, testing, and deployment.
Solidity: Language for writing smart contracts.
PHP: Backend for the frontend of the DApp.
Composer: Dependency management for PHP (if applicable).
ethers.js: JavaScript library for interacting with the Ethereum blockchain (used in PHP via JS integration).
