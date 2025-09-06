-- Create the main database
CREATE DATABASE IF NOT EXISTS insectabase;
USE insectabase;

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer'
);

-- Subfamilies table
CREATE TABLE subfamilies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url TEXT
);

-- Genes table
CREATE TABLE genes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    subfamily_id INT,
    region VARCHAR(100),
    description TEXT,
    image_url TEXT,
    FOREIGN KEY (subfamily_id) REFERENCES subfamilies(id) ON DELETE SET NULL
);

-- Species table
CREATE TABLE species (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    status VARCHAR(50),
    location VARCHAR(100),
    diagnosis TEXT,
    map_link TEXT,
    image_url TEXT,
    pdf_url TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    subfamily_id INT,
    gene_id INT,
    FOREIGN KEY (subfamily_id) REFERENCES subfamilies(id) ON DELETE SET NULL,
    FOREIGN KEY (gene_id) REFERENCES genes(id) ON DELETE SET NULL
);

-- Images table (additional photos for each species)
CREATE TABLE images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    species_id INT,
    url TEXT,
    caption VARCHAR(255),
    FOREIGN KEY (species_id) REFERENCES species(id) ON DELETE CASCADE
);

-- Literature table
CREATE TABLE literature (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    authors TEXT,
    year INT,
    link TEXT,
    pdf TEXT
);

-- News table
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    link TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Backgrounds table  
CREATE TABLE backgrounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_url TEXT NOT NULL,
    page VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contacts table
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

