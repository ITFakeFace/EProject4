/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.example.demo.repositories;

import com.example.demo.entities.SalaryDetail;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

public interface ISalaryDetailRepository extends JpaRepository<SalaryDetail, Integer> {

    SalaryDetail save(SalaryDetail detail);

    @Transactional
    @Modifying
    @Query(value = "delete SalaryDetail s where s.id = :id")
    void delete(@Param("id") Integer id);

    @Transactional
    @Modifying
    @Query(value = "DELETE FROM salary_detail WHERE salary_id = :salary_id", nativeQuery = true)
    void deleteBySalaryId(@Param("salary_id") Integer salary_id);

    @Transactional
    @Modifying
    @Query(value = "SELECT * FROM salary_detail WHERE salary_id = :salary_id", nativeQuery = true)
    List<SalaryDetail> findBySalaryId(@Param("salary_id") Integer salary_id);

    SalaryDetail findById(int id);

    @Transactional
    @Modifying
    @Query(value = "SELECT * FROM salary_detail WHERE staff_id = :staff_id", nativeQuery = true)
    List<SalaryDetail> findByStaffId(@Param("staff_id") Integer staff_id);
}
