-- Database changes for adding bank information to user system

-- Add bank-related columns to tuser table
ALTER TABLE tuser 
  ADD COLUMN kodebank varchar(25) NOT NULL DEFAULT '',
  ADD COLUMN norek varchar(25) NOT NULL DEFAULT '',
  ADD COLUMN namarek varchar(125) NOT NULL DEFAULT '';

-- Create tbank table
CREATE TABLE tbank (
  id int PRIMARY KEY AUTO_INCREMENT,
  kode varchar(25) NOT NULL DEFAULT '',
  nama varchar(125) NOT NULL DEFAULT ''
);

-- Insert some sample bank data
INSERT INTO tbank (kode, nama) VALUES 
('BCA', 'Bank Central Asia'),
('BNI', 'Bank Negara Indonesia'),
('BRI', 'Bank Rakyat Indonesia'),
('MAN', 'Bank Mandiri'),
('BTN', 'Bank Tabungan Negara'),
('DAN', 'Bank Danamon'),
('CIM', 'Bank CIMB Niaga'),
('PAN', 'Bank Panin'),
('PER', 'Bank Permata'),
('BSI', 'Bank Syariah Indonesia');
