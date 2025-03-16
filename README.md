# NFe Generation System

This is a web application designed for generating electronic invoices (NFe) for businesses. It provides a complete workflow from client and product registration, sales management, to invoice issuance and downloads. It includes multiple features for administration and user management, as well as integration for querying city data, product information, and client details.

## Features

### User Authentication & Authorization
- Authentication routes with login, logout, and password management.
- Admin panel accessible only to authenticated users, allowing for role-based access control.

### Client Management
- **List Clients**: View registered clients.
- **Register Client**: Add new clients to the system.
- **Edit Client**: Edit existing client details.
- **Delete Client**: Remove a client from the system.

### Product Management
- **List Products**: View registered products.
- **Register Product**: Add new products to the system.
- **Edit Product**: Edit existing product details.
- **Delete Product**: Remove a product from the system.

### Sale Management
- **List Sales**: View all sales transactions.
- **Register Sale**: Record new sales transactions.
- **Edit Sale**: Edit existing sale transactions.
- **Delete Sale**: Remove a sale from the system.
- **Generate NFe**: Issue electronic invoices (NFe) for sales, view issued invoices, and cancel invoices when needed.

### Nature Management
- **List Nature**: View registered nature of operations.
- **Register Nature**: Add new operation nature details.
- **Edit Nature**: Edit operation nature details.
- **Delete Nature**: Remove an operation nature from the system.

### Profile Management
- **Edit Profile**: Update user profile information.

### NFe Management
- **Issue NFe**: Generate a new NFe for a sale.
- **View NFe**: View the details of a generated NFe.
- **Cancel NFe**: Cancel a previously issued NFe.
- **Download NFe Files**: Download XML and PDF versions of the issued invoices.

### Search Routes
- **Search Cities**: Query cities within a specific state.
- **Search Products**: Query data related to a specific product.
- **Search Clients**: Query data related to a specific client.

### File Download
- **Download XML**: Download the XML version of the NFe.
- **Download PDF**: Download the PDF version of the NFe.

## Installation

To set up this application locally, follow these steps:

1. Clone the repository:
    ```bash
    git clone https://github.com/Henriquuepedro/nfe.git
    ```

2. Navigate to the project directory:
    ```bash
    cd nfe
    ```

3. Install dependencies using Composer:
    ```bash
    composer install
    ```

4. Copy the `.env.example` file to create a `.env` file:
    ```bash
    cp .env.example .env
    ```

5. Set up your database configuration in the `.env` file.

6. Generate the application key:
    ```bash
    php artisan key:generate
    ```

7. Run the database migrations:
    ```bash
    php artisan migrate
    ```

8. Start the local development server:
    ```bash
    php artisan serve
    ```

## Contributing

If you want to contribute to this project, feel free to fork it and submit a pull request. Ensure that your changes are well-tested and documented.

## License

This project is open-source and available under the MIT License.
