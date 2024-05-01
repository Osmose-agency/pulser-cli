# Pulser CLI

## Description
Pulser CLI is a command line interface designed to assist Osmose web developers in creating and managing Advanced Custom Fields (ACF) blocks for the Pulser WordPress theme. It facilitates connection to a private repository, allowing team members to import pre-built blocks into their projects efficiently.

## Features
- **Authenticate**: Securely connect to the Pulser block repository.
- **Check Authentication**: Verify current authentication status.
- **Create Block**: Scaffold new ACF blocks.
- **Import Block**: Import existing blocks from the repository into local projects.

## Requirements
- PHP ^8.0
- Symfony Console ^6.0|^7.0
- Symfony HttpClient ^6.0|^7.0

## Installation
To install Pulser CLI, run the following command:
```bash
composer global require osmose/pulser-cli
```

## Configuration
Configure Pulser CLI by setting up the necessary API tokens and other preferences required for connection to the Pulser repository.

### API Token Setup
To authenticate with the Pulser repository, developers need to configure their API token. Generate your personal token by visiting the Pulser Software at [pulser.osmose.net](https://pulser.osmose.net) and navigating to the profile section. Ensure you have an account on this platform. Once you have your token, use the following command to set it up:
```bash
pulser auth:token <your-token>
```
Replace `<your-token>` with the actual token you generated. This step is crucial for gaining access to the Pulser block repository and utilizing the CLI's features.

## Usage
After installation, use the following commands based on your needs:

- **Authenticate**: `pulser authenticate`
- **Check Authentication**: `pulser auth-check`
- **Create Block**: `pulser create-block`
- **Import Block**: `pulser import-block`

## License
This project is proprietary software. Unauthorized copying, modification, distribution, or use without explicit permission is prohibited.

## Contributing
If you are a member of the Osmose development team and would like to contribute to the development of Pulser CLI, please follow the standard pull request process and adhere to the coding standards set by Osmose.

## Support
For support, contact the Osmose technical support team or refer to the internal Osmose developer documentation.

This README provides a comprehensive guide to help developers get started with the Pulser CLI tool and make effective use of its features for WordPress development.