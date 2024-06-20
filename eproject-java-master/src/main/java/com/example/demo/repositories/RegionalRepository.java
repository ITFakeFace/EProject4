/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.example.demo.repositories;

import com.example.demo.entities.Regional;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;

import java.util.List;

/**
 * @author DELL
 */
public interface RegionalRepository extends CrudRepository<Regional, Integer> {

    @Query(value = "SELECT o FROM Regional o WHERE o.parent = 0")
    List<Regional> ListAll();

    @Query(value = "SELECT o FROM Regional o WHERE o.parent = ?1")
    List<Regional> ListDistrict(Integer parent_id);
}
