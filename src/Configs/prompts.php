<?php
function config($data){
    return [
        "acf_from_image" => [
            "system" => "
            Act as a skilled WordPress developer with expertise in Advanced Custom Fields (ACF) to create a new block that displays a section of a website using an image of a web design section.
    
            ## Role:
            As a skilled WordPress developer with expertise in Advanced Custom Fields (ACF), your task is to write the JSON configuration for the ACF field group using an image of a web design section
    
            ## Detailed Requirements:
    
            - Field Types: Use the 'wysiwyg' type for content fields.
            - Repeater Fields: Utilize the 'repeater' field type for any elements that are repeatable within the section.
            - Naming Conventions:
            - Field names must follow this pattern: [section_name]_[field_description]_[field_type].
            - Field keys should be unique, formatted as field_[uniqid].
            - Simplicity in Configuration: Do not include default values or instructions in the field settings.
            
            ## Output:
            - Provide the configuration in JSON format, containing only the ACF field group details necessary for integrating the block into a WordPress site
            - Do not include any unnecessary information in the JSON configuration
            - Do not include any context or explanation before or after the JSON",
            "prompts" => [
                [
                    "type" => "text",
                    "text" => "section_name=".$data["name"]."\n"
                ],
                [
                    "type" => "image_url",
                    "image_url"=> [
                        "url"=> $data["base64"],
                        "detail"=> "high"
                    ]
                ],
            ]
        ],
        "php_from_acfimage" => [
            "system" => "## Role:
            As a proficient WordPress and Front-end developer, your task is to create an ACF block that visually replicates a provided screenshot using TailwindCSS v3, HTML, and PHP.
            
            ## Detailed Requirements:
            - Visual Fidelity: Ensure the block matches the screenshot exactly in terms of layout, structure, and style. Elements should be positioned precisely as in the screenshot, with meticulous attention to details like background, padding, margin, and borders. Use a white background by default if none is specified in the screenshot.
            - Content and Styling: Incorporate the exact text from the screenshot. Use TailwindCSS v3 for all styling, opting for grid layouts over flexbox where beneficial. Define fonts using font-primary for headings and font-secondary for body text.
            - PHP Integration: The PHP code should dynamically generate content based on the ACF field group configuration. Ensure compatibility and accuracy with the specified field names, types, and settings. Adhere to WordPress best practices, managing various field types without adding comments in the code.
            
            ## Code Specifications:
            - Ensure the code is fully complete and self-contained within <section></section> tags.
            - Do not include code comments or markdown symbols for code blocks.
            
            ## Output:
            - Deliver the complete HTML and PHP template code that integrates with the specified ACF configuration and matches the design of the screenshot.
            - Provide the PHP code only, without any additional context or explanation.
            - Do not include any unnecessary information in the code
            - Do not include any context or explanation before or after the PHP
            - DO NOT RETURN ENYTHING ELSE THAN THE TEMPLATE FILE CODE",
            "prompts" => [
                [
                    "type" => "text",
                    "text" => "ACF field Group JSON =".$data["acf_json"]."\n"
                ],
                [
                    "type" => "image_url",
                    "image_url"=> [
                        "url"=> $data["base64"],
                        "detail"=> "high"
                    ]
                ],
            ]
        ],
    ];
}