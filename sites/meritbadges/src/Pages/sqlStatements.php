<?php

	$queryByMB_ALL = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
		ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName)
		WHERE counselors.Active = 'Yes' AND counselormerit.Status <> 'DROP'
		ORDER BY
			counselormerit.MeritName,
			counselors.LastName,
			counselors.FirstName;";

	$queryByCounselors = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
		ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName )
		WHERE counselors.Active = 'Yes' AND counselormerit.Status <> 'DROP'
		ORDER BY
			counselors.LastName,
			counselors.FirstName,
			counselormerit.MeritName;";	
	
	

//	$querySelectedCounselor1 = "SELECT DISTINCTROW counselors.LastName, counselors.FirstName, counselors.MemberID FROM counselors
//		INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
//		ON (counselors.LastName = counselormerit.LastName) AND (counselors.FirstName = counselormerit.FirstName)
//		WHERE counselors.Active='YES'
//		ORDER BY
//			counselors.LastName,
//			counselors.FirstName";
	$querySelectedCounselor1 = "SELECT DISTINCTROW counselors.LastName, counselors.FirstName, counselors.MemberID FROM counselors
	WHERE counselors.Active='YES'
	ORDER BY
		counselors.LastName,
		counselors.FirstName";
		
		
	$sqlQuery15 = "SELECT * FROM counselors INNER JOIN counselormerit ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName)
		WHERE counselors.Active = 'YES' AND counselormerit.Status <> 'DROP'
		GROUP BY counselormerit.LastName, counselormerit.FirstName, counselors.HomeDistrict, counselors.HomeTroop
		HAVING (((Count(counselormerit.MeritName))>15))";
	
	$sqlQueryMBCperMB = "SELECT * FROM counselors INNER JOIN counselormerit ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName)
		WHERE counselors.Active = 'YES' AND counselormerit.Status <> 'DROP'
		ORDER BY meritbadges.MeritName";
