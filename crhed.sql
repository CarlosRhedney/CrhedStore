-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18-Set-2023 às 03:17
-- Versão do servidor: 10.4.24-MariaDB
-- versão do PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `crhed`
--

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_addresses_save` (`pidaddress` INT(11), `pidperson` INT(11), `paddress` VARCHAR(128), `pnumber` DECIMAL(10), `pcomplement` VARCHAR(32), `pcity` VARCHAR(32), `pstate` VARCHAR(32), `pcountry` VARCHAR(32), `pzipcode` CHAR(8), `pdistrict` VARCHAR(32))   BEGIN

	IF pidaddress > 0 THEN
		
		UPDATE tb_addresses
        SET
			idperson = pidperson,
            address = paddress,
            number = pnumber,
            complement = pcomplement,
            city = pcity,
            state = pstate,
            country = pcountry,
            zipcode = pzipcode, 
            district = pdistrict
		WHERE idaddress = pidaddress;
        
    ELSE
		
		INSERT INTO tb_addresses (idperson, address, number, complement, city, state, country, zipcode, district)
        VALUES(pidperson, paddress, pnumber, pcomplement, pcity, pstate, pcountry, pzipcode, pdistrict);
        
        SET pidaddress = LAST_INSERT_ID();
        
    END IF;
    
    SELECT * FROM tb_addresses WHERE idaddress = pidaddress;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_carts_save` (`pidcart` INT, `psessionid` VARCHAR(64), `piduser` INT, `pzipcode` CHAR(8), `pfreight` DECIMAL(10,2), `pnrdays` INT)   BEGIN

    IF pidcart > 0 THEN
        
        UPDATE tb_carts
        SET
            sessionid = psessionid,
            iduser = piduser,
            zipcode = pzipcode,
            freight = pfreight,
            nrdays = pnrdays
        WHERE idcart = pidcart;
        
    ELSE
        
        INSERT INTO tb_carts (sessionid, iduser, zipcode, freight, nrdays)
        VALUES(psessionid, piduser, pzipcode, pfreight, pnrdays);
        
        SET pidcart = LAST_INSERT_ID();
        
    END IF;
    
    SELECT * FROM tb_carts WHERE idcart = pidcart;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categories_save` (`pidcategory` INT, `pcategory` VARCHAR(64))   BEGIN
	
	IF pidcategory > 0 THEN
		
		UPDATE tb_categories
        SET category = pcategory
        WHERE idcategory = pidcategory;
        
    ELSE
		
		INSERT INTO tb_categories (category) VALUES(pcategory);
        
        SET pidcategory = LAST_INSERT_ID();
        
    END IF;
    
    SELECT * FROM tb_categories WHERE idcategory = pidcategory;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_data_save` (`piddata` INT(11), `pidperson` INT(11), `prg` VARCHAR(9), `pcpf` VARCHAR(11))   BEGIN

	IF piddata > 0 THEN
		
		UPDATE tb_data
        SET
			idperson = pidperson,
            rg = prg,
            cpf = pcpf
		WHERE iddata = piddata;
        
    ELSE
		
		INSERT INTO tb_data (idperson, rg, cpf)
        VALUES(pidperson, prg, pcpf);
        
        SET piddata = LAST_INSERT_ID();
        
    END IF;
    
    SELECT * FROM tb_addata WHERE iddata = piddata;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ongs_save` (`pidong` INT(11), `pidperson` INT(11), `pong` VARCHAR(64), `pcnpj` VARCHAR(18), `plogradouro` VARCHAR(128), `pcity` VARCHAR(64), `pnumber` DECIMAL(10), `purl` VARCHAR(128))   BEGIN
    
    IF pidong > 0 THEN
        
        UPDATE tb_ongs
        SET 
	idperson = pidperson,
        ong = pong,
            cnpj = pcnpj,
            logradouro = plogradouro,
            city = pcity,
            number = pnumber,
            url = purl
        WHERE idong = pidong;
    
    ELSE
        
        INSERT INTO tb_ongs (idperson, ong, cnpj, logradouro, city, number, url) 
        VALUES(pidperson, pong, plogradouro, pcnpj, pcity, pnumber, purl);
        
        SET pidong = LAST_INSERT_ID();
        
    END IF;
    
    SELECT * FROM tb_ongs WHERE idong = pidong;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_products_save` (`pidproduct` INT(11), `pproduct` VARCHAR(64), `pprice` DECIMAL(10,2), `pwidth` DECIMAL(10,2), `pheight` DECIMAL(10,2), `plength` DECIMAL(10,2), `pweight` DECIMAL(10,2), `purl` VARCHAR(128))   BEGIN
    
    IF pidproduct > 0 THEN
        
        UPDATE tb_products
        SET 
            product = pproduct,
            price = pprice,
            width = pwidth,
            height = pheight,
            length = plength,
            weight = pweight,
            url = purl
        WHERE idproduct = pidproduct;
        
    ELSE
        
        INSERT INTO tb_products (product, price, width, height, length, weight, url) 
        VALUES(pproduct, pprice, pwidth, pheight, plength, pweight, purl);
        
        SET pidproduct = LAST_INSERT_ID();
        
    END IF;
    
    SELECT * FROM tb_products WHERE idproduct = pidproduct;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_userspasswordsrecoveries_create` (`piduser` INT, `pip` VARCHAR(45))   BEGIN
	
	INSERT INTO tb_userspasswordsrecoveries (iduser, ip)
    VALUES(piduser, pip);
    
    SELECT * FROM tb_userspasswordsrecoveries
    WHERE idrecovery = LAST_INSERT_ID();
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usersupdate_save` (`piduser` INT, `pperson` VARCHAR(64), `plogin` VARCHAR(64), `pdespassword` VARCHAR(256), `pmail` VARCHAR(128), `pnrphone` BIGINT, `pinadmin` TINYINT)   BEGIN
  
    DECLARE vidperson INT;
    
  SELECT idperson INTO vidperson
    FROM tb_users
    WHERE iduser = piduser;
    
    UPDATE tb_persons
    SET 
    person = pperson,
        mail = pmail,
        nrphone = pnrphone
  WHERE idperson = vidperson;
    
    UPDATE tb_users
    SET
    login = plogin,
        despassword = pdespassword,
        inadmin = pinadmin
  WHERE iduser = piduser;
    
    SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = piduser;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_delete` (`piduser` INT)   BEGIN
  
    DECLARE vidperson INT;
    
  SELECT idperson INTO vidperson
    FROM tb_users
    WHERE iduser = piduser;
    
    DELETE FROM tb_users WHERE iduser = piduser;
    DELETE FROM tb_persons WHERE idperson = vidperson;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_users_save` (`pperson` VARCHAR(64), `plogin` VARCHAR(64), `pdespassword` VARCHAR(256), `pmail` VARCHAR(128), `pnrphone` BIGINT, `pinadmin` TINYINT)   BEGIN
  
    DECLARE vidperson INT;
    
  INSERT INTO tb_persons (person, mail, nrphone)
    VALUES(pperson, pmail, pnrphone);
    
    SET vidperson = LAST_INSERT_ID();
    
    INSERT INTO tb_users (idperson, login, despassword, inadmin)
    VALUES(vidperson, plogin, pdespassword, pinadmin);
    
    SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = LAST_INSERT_ID();
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_addresses`
--

CREATE TABLE `tb_addresses` (
  `idaddress` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `address` varchar(128) NOT NULL,
  `number` decimal(10,0) NOT NULL,
  `complement` varchar(32) DEFAULT NULL,
  `city` varchar(32) NOT NULL,
  `state` varchar(32) NOT NULL,
  `country` varchar(32) NOT NULL,
  `zipcode` char(8) NOT NULL,
  `district` varchar(32) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_carts`
--

CREATE TABLE `tb_carts` (
  `idcart` int(11) NOT NULL,
  `sessionid` varchar(64) NOT NULL,
  `iduser` int(11) DEFAULT NULL,
  `zipcode` char(8) DEFAULT NULL,
  `freight` decimal(10,2) DEFAULT NULL,
  `nrdays` int(11) DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_carts`
--

INSERT INTO `tb_carts` (`idcart`, `sessionid`, `iduser`, `zipcode`, `freight`, `nrdays`, `dtregister`) VALUES
(1, 'k1udns4fipregt9ap3al8qrmke', 1, '04828190', '0.00', 0, '2023-09-18 01:06:44');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_cartsproducts`
--

CREATE TABLE `tb_cartsproducts` (
  `idcartproduct` int(11) NOT NULL,
  `idcart` int(11) NOT NULL,
  `idproduct` int(11) NOT NULL,
  `dtremoved` datetime DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_cartsproducts`
--

INSERT INTO `tb_cartsproducts` (`idcartproduct`, `idcart`, `idproduct`, `dtremoved`, `dtregister`) VALUES
(1, 1, 5, '2023-09-17 22:07:51', '2023-09-18 01:06:44'),
(2, 1, 5, '2023-09-17 22:07:51', '2023-09-18 01:07:34'),
(3, 1, 3, '2023-09-17 22:08:56', '2023-09-18 01:08:17');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_categories`
--

CREATE TABLE `tb_categories` (
  `idcategory` int(11) NOT NULL,
  `category` varchar(64) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_categories`
--

INSERT INTO `tb_categories` (`idcategory`, `category`, `dtregister`) VALUES
(1, 'Apple', '2023-09-18 00:52:10'),
(2, 'Samsung', '2023-09-18 00:52:29'),
(3, 'Nokia', '2023-09-18 00:52:47'),
(4, 'LG', '2023-09-18 00:59:18');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_categoriesproducts`
--

CREATE TABLE `tb_categoriesproducts` (
  `idcategory` int(11) NOT NULL,
  `idproduct` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_categoriesproducts`
--

INSERT INTO `tb_categoriesproducts` (`idcategory`, `idproduct`) VALUES
(1, 2),
(2, 1),
(3, 3),
(4, 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_data`
--

CREATE TABLE `tb_data` (
  `iddata` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `rg` varchar(9) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_feedback`
--

CREATE TABLE `tb_feedback` (
  `idfeed` int(11) NOT NULL,
  `cod` char(4) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_persons`
--

CREATE TABLE `tb_persons` (
  `idperson` int(11) NOT NULL,
  `person` varchar(64) NOT NULL,
  `mail` varchar(128) DEFAULT NULL,
  `nrphone` bigint(20) DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_persons`
--

INSERT INTO `tb_persons` (`idperson`, `person`, `mail`, `nrphone`, `dtregister`) VALUES
(1, 'Carlos Rhedney', 'twisterpsa@hotmail.com', 11970585566, '2021-08-13 06:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_photos`
--

CREATE TABLE `tb_photos` (
  `idphoto` int(11) NOT NULL,
  `idproduct` int(11) NOT NULL,
  `photo` varchar(64) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_photos`
--

INSERT INTO `tb_photos` (`idphoto`, `idproduct`, `photo`, `dtregister`) VALUES
(1, 1, 'assets/site/img/products/Motorola G 2.jpg', '2023-09-18 00:55:46'),
(2, 2, 'assets/site/img/products/Apple Branco 128 GB 8 RAM.jpg', '2023-09-18 00:57:19'),
(3, 3, 'assets/site/img/products/Nokia Preto 32 GB 2 RAM.jpg', '2023-09-18 00:59:09'),
(4, 4, 'assets/site/img/products/LG Cinza 64 GB 4 RAM.jpg', '2023-09-18 01:00:40'),
(5, 5, 'assets/site/img/products/Smartphone Sony 32 GB 4 RAM.jpg', '2023-09-18 01:02:04'),
(6, 1, 'assets/site/img/products/Samsung G 2.jpg', '2023-09-18 01:04:27'),
(7, 1, 'assets/site/img/products/Samsung G 2.jpg', '2023-09-18 01:05:36');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_products`
--

CREATE TABLE `tb_products` (
  `idproduct` int(11) NOT NULL,
  `product` varchar(64) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `width` decimal(10,2) NOT NULL,
  `height` decimal(10,2) NOT NULL,
  `length` decimal(10,2) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `url` varchar(128) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_products`
--

INSERT INTO `tb_products` (`idproduct`, `product`, `price`, `width`, `height`, `length`, `weight`, `url`, `dtregister`) VALUES
(1, 'Samsung G 2', '1500.00', '0.10', '0.05', '0.90', '100.00', 'Motorola-G2', '2023-09-18 00:55:46'),
(2, 'Apple Branco 128 GB 8 RAM', '3500.00', '0.30', '0.10', '0.80', '90.00', 'Apple Branco-128-GB-8-RAM', '2023-09-18 00:57:18'),
(3, 'Nokia Preto 32 GB 2 RAM', '1800.00', '0.40', '0.20', '0.90', '90.00', 'Nokia-Preto-32-GB-2-RAM', '2023-09-18 00:59:09'),
(4, 'LG Cinza 64 GB 4 RAM', '1320.00', '0.60', '0.20', '1.00', '100.00', 'LG Cinza-64-GB-4-RAM', '2023-09-18 01:00:40'),
(5, 'Smartphone Sony 32 GB 4 RAM', '1900.00', '0.90', '0.30', '1.00', '200.00', 'Smartphone-Sony-32-GB-4-RAM', '2023-09-18 01:02:04');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_users`
--

CREATE TABLE `tb_users` (
  `iduser` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `login` varchar(64) NOT NULL,
  `despassword` varchar(256) NOT NULL,
  `inadmin` tinyint(4) NOT NULL DEFAULT 0,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_users`
--

INSERT INTO `tb_users` (`iduser`, `idperson`, `login`, `despassword`, `inadmin`, `dtregister`) VALUES
(1, 1, 'IronMan', '$2y$12$YlooCyNvyTji8bPRcrfNfOKnVMmZA9ViM2A3IpFjmrpIbp5ovNmga', 1, '2021-08-13 06:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_userspasswordsrecoveries`
--

CREATE TABLE `tb_userspasswordsrecoveries` (
  `idrecovery` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `dtrecovery` datetime DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `tb_addresses`
--
ALTER TABLE `tb_addresses`
  ADD PRIMARY KEY (`idaddress`),
  ADD KEY `fk_addresses_persons_idx` (`idperson`);

--
-- Índices para tabela `tb_carts`
--
ALTER TABLE `tb_carts`
  ADD PRIMARY KEY (`idcart`),
  ADD KEY `FK_carts_users_idx` (`iduser`);

--
-- Índices para tabela `tb_cartsproducts`
--
ALTER TABLE `tb_cartsproducts`
  ADD PRIMARY KEY (`idcartproduct`),
  ADD KEY `FK_cartsproducts_carts_idx` (`idcart`),
  ADD KEY `FK_cartsproducts_products_idx` (`idproduct`);

--
-- Índices para tabela `tb_categories`
--
ALTER TABLE `tb_categories`
  ADD PRIMARY KEY (`idcategory`);

--
-- Índices para tabela `tb_categoriesproducts`
--
ALTER TABLE `tb_categoriesproducts`
  ADD PRIMARY KEY (`idcategory`,`idproduct`);

--
-- Índices para tabela `tb_data`
--
ALTER TABLE `tb_data`
  ADD PRIMARY KEY (`iddata`),
  ADD KEY `fk_data_persons_idx` (`idperson`);

--
-- Índices para tabela `tb_feedback`
--
ALTER TABLE `tb_feedback`
  ADD PRIMARY KEY (`idfeed`);

--
-- Índices para tabela `tb_persons`
--
ALTER TABLE `tb_persons`
  ADD PRIMARY KEY (`idperson`);

--
-- Índices para tabela `tb_photos`
--
ALTER TABLE `tb_photos`
  ADD PRIMARY KEY (`idphoto`);

--
-- Índices para tabela `tb_products`
--
ALTER TABLE `tb_products`
  ADD PRIMARY KEY (`idproduct`);

--
-- Índices para tabela `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`iduser`),
  ADD KEY `FK_users_persons_idx` (`idperson`);

--
-- Índices para tabela `tb_userspasswordsrecoveries`
--
ALTER TABLE `tb_userspasswordsrecoveries`
  ADD PRIMARY KEY (`idrecovery`),
  ADD KEY `fk_userspasswordsrecoveries_users_idx` (`iduser`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tb_addresses`
--
ALTER TABLE `tb_addresses`
  MODIFY `idaddress` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_carts`
--
ALTER TABLE `tb_carts`
  MODIFY `idcart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_cartsproducts`
--
ALTER TABLE `tb_cartsproducts`
  MODIFY `idcartproduct` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tb_categories`
--
ALTER TABLE `tb_categories`
  MODIFY `idcategory` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tb_data`
--
ALTER TABLE `tb_data`
  MODIFY `iddata` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_feedback`
--
ALTER TABLE `tb_feedback`
  MODIFY `idfeed` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_persons`
--
ALTER TABLE `tb_persons`
  MODIFY `idperson` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `tb_photos`
--
ALTER TABLE `tb_photos`
  MODIFY `idphoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `tb_products`
--
ALTER TABLE `tb_products`
  MODIFY `idproduct` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `tb_userspasswordsrecoveries`
--
ALTER TABLE `tb_userspasswordsrecoveries`
  MODIFY `idrecovery` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `tb_addresses`
--
ALTER TABLE `tb_addresses`
  ADD CONSTRAINT `fk_addresses_persons` FOREIGN KEY (`idperson`) REFERENCES `tb_persons` (`idperson`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `tb_carts`
--
ALTER TABLE `tb_carts`
  ADD CONSTRAINT `fk_carts_users` FOREIGN KEY (`iduser`) REFERENCES `tb_users` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `tb_cartsproducts`
--
ALTER TABLE `tb_cartsproducts`
  ADD CONSTRAINT `fk_cartsproducts_carts` FOREIGN KEY (`idcart`) REFERENCES `tb_carts` (`idcart`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_cartsproducts_products` FOREIGN KEY (`idproduct`) REFERENCES `tb_products` (`idproduct`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `tb_data`
--
ALTER TABLE `tb_data`
  ADD CONSTRAINT `fk_data_persons` FOREIGN KEY (`idperson`) REFERENCES `tb_persons` (`idperson`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `tb_users`
--
ALTER TABLE `tb_users`
  ADD CONSTRAINT `fk_users_persons` FOREIGN KEY (`idperson`) REFERENCES `tb_persons` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `tb_userspasswordsrecoveries`
--
ALTER TABLE `tb_userspasswordsrecoveries`
  ADD CONSTRAINT `fk_userspasswordsrecoveries_users` FOREIGN KEY (`iduser`) REFERENCES `tb_users` (`iduser`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
