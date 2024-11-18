const { buildModule } = require("@nomicfoundation/hardhat-ignition/modules");

module.exports = buildModule("CharityModule", (m) => {
    const cert = m.contract("CharityDonationApp");
    return { cert };
});
