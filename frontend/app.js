document.addEventListener('DOMContentLoaded', () => {
    // DOM elements
    const connectMetaMask = document.getElementById('connectMetaMask');
    const donateButton = document.getElementById('donateButton');
    const donationPopup = document.getElementById('donationPopup');
    const closePopup = document.getElementById('closePopup');
    const receiverForm = document.getElementById('receiverForm');
    const verificationStatus = document.getElementById('verificationStatus');
    const viewStatusButton = document.getElementById('viewStatus');
    const withdrawButton = document.getElementById('withdrawButton');
    const requestStatus = document.getElementById('requestStatus');
    const receiverAddressInput = document.getElementById('receiverAddress');
    const donationAmountInput = document.getElementById('donationAmount');

    let userAddress;
    let web3;
    let CharityContract;

    const abi = [
        {
            "inputs": [
                { "internalType": "uint256", "name": "_maxAmount", "type": "uint256" }
            ],
            "name": "createCharityRequest",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [{ "internalType": "address", "name": "_donor", "type": "address" }],
            "name": "getDonorDetails",
            "outputs": [
                { "internalType": "string", "name": "name", "type": "string" },
                { "internalType": "uint256", "name": "donatedAmount", "type": "uint256" }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "viewAllDonors",
            "outputs": [
                {
                    "internalType": "address[]",
                    "name": "",
                    "type": "address[]"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        }
    ];

    const contractAddress = "0x8Dcd577D68C9851bda157Eb7f26d5ab2D05834f1";

    async function initializeWeb3() {
        if (typeof window.ethereum !== 'undefined') {
            web3 = new Web3(window.ethereum);
            CharityContract = new web3.eth.Contract(abi, contractAddress);
        } else {
            alert('MetaMask is not installed. Please install MetaMask to continue.');
        }
    }

    // Connect to MetaMask
    if (connectMetaMask) {
        connectMetaMask.addEventListener('click', async () => {
            if (typeof window.ethereum !== 'undefined') {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    userAddress = accounts[0];
                    alert(`Connected to MetaMask: ${userAddress}`);
                } catch (error) {
                    console.error('MetaMask connection error:', error);
                    alert('Failed to connect to MetaMask.');
                }
            } else {
                alert('MetaMask is not installed. Please install MetaMask to continue.');
            }
        });
    }

    // Handle donation button click
    if (donateButton) {
        donateButton.addEventListener('click', async () => {
            const receiverAddress = receiverAddressInput.value;
            const donationAmount = donationAmountInput.value;

            if (receiverAddress && donationAmount) {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    const tx = await CharityContract.methods
                        .createCharityRequest(donationAmount)
                        .send({ from: accounts[0], gas: 500000 });

                    console.log('Donation Transaction:', tx);
                    alert('Donation successful!');
                } catch (error) {
                    console.error('Error during donation:', error);
                    alert('Donation failed.');
                }
            } else {
                alert('Please fill in both the receiver address and donation amount.');
            }
        });
    }

    // Close donation popup
    if (closePopup) {
        closePopup.addEventListener('click', () => {
            donationPopup.classList.add('hidden');
        });
    }

    // Handle receiver form submission
    if (receiverForm) {
        receiverForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const idProofType = document.getElementById('idProofType').value;
            const idProofFile = document.getElementById('idProofFile').files[0];

            if (idProofType && idProofFile) {
                verificationStatus.classList.remove('hidden');
                alert('ID proof submitted successfully for verification.');
            } else {
                alert('Please select a valid ID proof type and upload the corresponding file.');
            }
        });
    }

    // Handle view status button click
    if (viewStatusButton) {
        viewStatusButton.addEventListener('click', async () => {
            try {
                const allDonors = await CharityContract.methods.viewAllDonors().call();
                alert(`List of All Donors: \n${allDonors.join('\n')}`);
            } catch (error) {
                console.error('Error fetching donor list:', error);
                alert('Failed to fetch donor list.');
            }
        });
    }

    // Handle withdraw request
    if (withdrawButton) {
        withdrawButton.addEventListener('click', () => {
            requestStatus.textContent = "Approved";
            alert("Withdrawal approved. Amount transferred successfully!");
        });
    }

    initializeWeb3();
});
