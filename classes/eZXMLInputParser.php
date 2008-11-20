<?php
//
// Definition of eZDHTMLXMLInput class
//
// Created on: <27-Mar-2006 15:28:40 ks>
//
// Copyright (C) 1999-2006 eZ systems as. All rights reserved.
//

/*! \file ezdhtmlxmlinput.php
*/

/*!
  \class eZDHTMLXMLInput
  \brief The class eZDHTMLXMLInput does

*/

if(!class_exists('eZXMLSchema'))
	//include_once( 'extension/ezdhtml/ezxmltext/common/ezxmlschema.php' );
	

if(!class_exists('eZXMLInputParser'))
	include_once( 'kernel/classes/datatypes/ezxmltext/ezxmlinputparser.php' );
	//include_once( 'extension/ezdhtml/ezxmltext/common/ezxmlinputparser.php' );


class DataImporteZXMLInputParser extends eZXMLInputParser
{
    var $InputTags = array(
        'section' => array( 'name' => 'section' ),
        'b'       => array( 'name' => 'strong' ),
        'bold'    => array( 'name' => 'strong' ),
        'strong'  => array( 'name' => 'strong' ),
        'i'       => array( 'name' => 'emphasize' ),
        'em'      => array( 'name' => 'emphasize' ),
        'img'     => array( 'nameHandler' => 'tagNameImg',
                            'noChildren' => true ),
        'h1'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h2'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h3'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h4'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h5'      => array( 'nameHandler' => 'tagNameHeader' ),
        'h6'      => array( 'nameHandler' => 'tagNameHeader' ),
        'p'       => array( 'name' => 'paragraph' ),
        'br'      => array( 'name' => 'br',
                            'noChildren' => true ),
        'span'    => array( 'nameHandler' => 'tagNameSpan' ),
        'table'   => array( 'nameHandler' => 'tagNameTable' ),
        'td'      => array( 'name' => 'td' ),
        'tr'      => array( 'name' => 'tr' ),
        'th'      => array( 'name' => 'th' ),
        'ol'      => array( 'name' => 'ol' ),
        'ul'      => array( 'name' => 'ul' ),
        'li'      => array( 'name' => 'li' ),
        'a'       => array( 'name' => 'link' ),
        'link'    => array( 'name' => 'link' ),
       // Stubs for not supported tags.
        'tbody'   => array( 'name' => '' )
        );

    var $OutputTags = array(
        'section'   => array(),

        'embed'     => array( 'structHandler' => 'appendLineParagraph',
                              'publishHandler' => 'publishHandlerEmbed',
                              'attributes' => array( 'alt' => 'size',
                                                     'html_id' => 'xhtml:id' ) ),

        'embed-inline' => array( 'structHandler' => 'appendLineParagraph',
                              'publishHandler' => 'publishHandlerEmbed',
                              'attributes' => array( 'alt' => 'size',
                                                     'html_id' => 'xhtml:id' ) ),

        'object'    => array( 'structHandler' => 'structHandlerObject',
                              'publishHandler' => 'publishHandlerObject',
                              'attributes' => array( 'alt' => 'size' ) ),

        'table'     => array( 'structHandler' => 'appendParagraph',
                              'publishHandler' => 'publishHandlerTable',
                              'attributes' => array( 'border' => false,
                                                     'ezborder' => 'border' ) ),

        'tr'        => array(),

        'td'        => array( 'attributes' => array( 'width' => 'xhtml:width',
                                                     'colspan' => 'xhtml:colspan',
                                                     'rowspan' => 'xhtml:rowspan' ) ),

        'th'        => array( 'attributes' => array( 'width' => 'xhtml:width',
                                                     'colspan' => 'xhtml:colspan',
                                                     'rowspan' => 'xhtml:rowspan' ) ),

        'ol'        => array( 'structHandler' => 'structHandlerLists' ),

        'ul'        => array( 'structHandler' => 'structHandlerLists' ),

        'li'        => array( 'autoCloseOn' => array( 'li' ) ),

        'header'    => array( 'initHandler' => 'initHandlerHeader',
                              'autoCloseOn' => array( 'paragraph' ),
                              'structHandler' => 'structHandlerHeader' ),

        'paragraph' => array( 'autoCloseOn' => array( 'paragraph' ),
                              'structHandler' => 'structHandlerParagraph' ),

        'line'      => array(),

        'br'        => array( 'parsingHandler' => 'breakInlineFlow',
                              'structHandler' => 'structHandlerBr',
                              'attributes' => false ),

        'literal'   => array( 'parsingHandler' => 'parsingHandlerLiteral',
                              'structHandler' => 'appendParagraph',
                              'attributes' => array( 'class' => 'class',
                                                     'title' => 'class' ) ),

        'strong'    => array( 'structHandler' => 'appendLineParagraph' ),

        'emphasize' => array( 'structHandler' => 'appendLineParagraph' ),

        'link'      => array( 'structHandler' => 'appendLineParagraph',
                              'publishHandler' => 'publishHandlerLink',
                              'attributes' => array( 'title' => 'xhtml:title',
                                                     'id' => 'xhtml:id' ) ),

        'anchor'    => array( 'structHandler' => 'appendLineParagraph' ),

        'custom'    => array( //'parsingHandler' => 'parsingHandlerCustom',
                              'initHandler' => 'initHandlerCustom',
                              'structHandler' => 'structHandlerCustom',
                              'attributes' => array( 'title' => 'name' ) ),

        '#text'     => array( 'structHandler' => 'structHandlerText' )
        );

    function DataImporteZXMLInputParser( $validate = false, $errorLevel = EZ_XMLINPUTPARSER_SHOW_NO_ERRORS,
                                 $parseLineBreaks = false, $removeDefaultAttrs = true )
    {
        $this->eZXMLInputParser( $validate, $errorLevel, $parseLineBreaks, $removeDefaultAttrs );
		
		if ( $this->eZPublishVersion >= 3.8 )
        {
            $ini =& eZINI::instance( 'content.ini' );
            $this->anchorAsAttribute = $ini->variable( 'header', 'AnchorAsAttribute' ) == 'disabled' ? false : true;
        }

        if ( $this->eZPublishVersion < 3.9 )
        {
            // Literal was inline before 3.9
            $this->OutputTags['literal']['structHandler'] = 'appendLineParagraph';
        }
    }

    /*
        Name handlers (called at pass 1)
    */
       
    function &tagNameSpan( $tagName, &$attributes )
    {
        $name = '';
        if ( isset( $attributes['type'] ) && $attributes['type'] == 'custom' )
        {
            $name = 'custom';
            $attributes['children_required'] = 'true';
        }
        else if ( isset( $attributes['style'] ) )
        {
            if ( strstr( $attributes['style'], 'font-weight: bold;' ) )
                $name = 'strong';
            elseif ( strstr( $attributes['style'], 'font-style: italic;' ) )
                $name = 'emphasize';
        }
        return $name;
    }

    function &tagNameHeader( $tagName, &$attributes )
    {
        $attributes['level'] = $tagName[1];
        $name = 'header';
        return $name;
    }

    function &tagNameTable( $tagName, &$attributes )
    {
        $name = 'table';

        if ( isset( $attributes['id'] ) )
        {
            if ( $attributes['id'] == 'literal' )
            {
                $name = 'literal';
                return $name;
            }
            elseif ( $attributes['id'] == 'custom' )
            {
                $name = 'custom';
                return $name;
            }
        }
        if ( isset( $attributes['border'] ) && !isset( $attributes['ezborder'] ) )
        {
            $attributes['ezborder'] = $attributes['border'];
        }
        return $name;
    }
    
    function &tagNameImg( $tagName, &$attributes )
    {        
        $name = '';
        if ( isset( $attributes['id'] ) )
        {
            if ( strstr( $attributes['id'], 'eZObject_' ) || strstr( $attributes['id'], 'eZNode_' ) )
                if ( $this->eZPublishVersion >= 3.6 )
                {
                    if ( $this->eZPublishVersion >= 3.8 &&
                         isset( $attributes['inline'] ) &&
                         $attributes['inline'] == 'true' )
                    {
                        $name = 'embed-inline';
                    }
                    else
                    {
                        $name = 'embed';
                    }
                }
                else
                {
                    $name = 'object';
                }
        }
        if ( isset( $attributes['type'] ) )
        {
            if ( $attributes['type'] == 'anchor' )
                $name = 'anchor';
            elseif ( $attributes['type'] == 'custom' )
                $name = 'custom';
        }

        return $name;
    }

    /*
        Parsing Handlers (called at pass 1)
    */
    function &parsingHandlerLiteral( &$element, &$param )
    {

        $ret = null;
        $data =& $param[0];
        $pos =& $param[1];
				
        $tablePos = strpos( $data, '</literal>', $pos );
        if ( $tablePos === false )
            $tablePos = strpos( $data, '</LITERAL>', $pos );

        if ( $tablePos === false )
            return $ret;
            
        $tag = substr( $data, $pos - strlen('<literal class="html">'), strlen('<literal class="html">') );
               
        $text = substr( $data, $pos, $tablePos - $pos );
    
                
        $text = preg_replace( "/\s*<\s?\/?t[drh|(body)].*?>/i", "", $text );

        $text = preg_replace( "/^<p.*?>/i", "", $text );

        $text = preg_replace( "/<\/\s?p>/i", "", $text );

        $text = preg_replace( "/<p.*?>/i", "\n\n", $text );
        $text = preg_replace( "/<\/?\s?br.*?>/i", "\n", $text );

        $text = $this->entitiesDecode( $text );
        $text = $this->convertNumericEntities( $text );
		
        $textNode = $this->Document->createTextNode( $text );       
        $element->appendChild( $textNode );
        
        $pos = $tablePos + strlen( '</literal>' );
        $ret = false;

        return $ret;
    }

    function &breakInlineFlow( &$element, &$param )
    {
        // Breaks the flow of inline tags. Used for non-inline tags caught within inline.
        // Works for tags with no children only.
        $ret = null;
        $data =& $param[0];
        $pos =& $param[1];
        $tagBeginPos =& $param[2];
        $parent =& $element->parentNode;

        $wholeTagString = substr( $data, $tagBeginPos, $pos - $tagBeginPos );

        if ( $parent &&
             //!$this->XMLSchema->isInline( $element ) &&
             $this->XMLSchema->isInline( $parent ) //&&
             //!$this->XMLSchema->check( $parent, $element )
             )
        {
            $insertData = '';
            $currentParent =& $parent;
            end( $this->ParentStack );
            do
            {
                $stackData = current( $this->ParentStack );
                $currentParentName = $stackData[0];
                $insertData .= "</$currentParentName>";
                $currentParent =& $currentParent->parentNode;
                prev( $this->ParentStack );
            }
            while( $this->XMLSchema->isInline( $currentParent ) );

            $insertData .= $wholeTagString;

            $currentParent =& $parent;
            end( $this->ParentStack );
            $appendData = '';
            do
            {
                $stackData = current( $this->ParentStack );
                $currentParentName = $stackData[0];
                $currentParentAttrString = '';
                if ( $stackData[2] )
                    $currentParentAttrString = ' ' . $stackData[2];
                $appendData = "<$currentParentName$currentParentAttrString>" . $appendData;
                $currentParent =& $currentParent->parentNode;
                prev( $this->ParentStack );
            }
            while( $this->XMLSchema->isInline( $currentParent ) );

            $insertData .= $appendData;

            $data = $insertData . substr( $data, $pos );
            $pos = 0;
            $parent->removeChild( $element );
            $ret = false;
        }

        return $ret;
    }

    /*
        Init handlers. (called at pass 2)
    */
    // Init handler for 'Custom' element.
    function &initHandlerCustom( &$element, &$params )
    {
        $ret = null;
        if ( $this->XMLSchema->isInline( $element ) )
            return $ret;

        $tr =& $element->firstChild();
        if ( !$tr || $tr->nodeName != 'tr' )
            return $ret;

        $td =& $tr->firstChild();
        if ( $td->nodeName != 'td' )
            return $ret;

        // php5 TODO: children
        foreach( array_keys( $td->Children ) as $key )
        {
            $child =& $td->Children[$key];
            $td->removeChild( $child );
            $element->appendChild( $child );
        }
        $element->removeChild( $tr );

        return $ret;
    }

    function &initHandlerHeader( &$element, &$params )
    {
        $ret = null;

        if ( $this->anchorAsAttribute )
        {
            $anchorElement =& $element->firstChild();
            if ( $anchorElement->nodeName == 'anchor' )
            {
                $element->setAttribute( 'anchor_name', $anchorElement->getAttribute( 'name' ) );
                $element->removeChild( $anchorElement );
            }
        }

        return $ret;
    }

    /*
        Structure handlers. (called at pass 2)
    */
    // Structure handler for inline nodes.
    function &appendLineParagraph( &$element, &$newParent )
    {
        $ret = array();
        $parent =& $element->parentNode;
        if ( !$parent )
            return $ret;

        $parentName = $parent->nodeName;
        $next =& $element->nextSibling();
        $newParentName = $newParent != null ? $newParent->nodeName : '';

        // Correct schema by adding <line> and <paragraph> tags.
        if ( $parentName == 'line' || $this->XMLSchema->isInline( $parent ) )
        {
            return $ret;
        }

        if ( $newParentName == 'line' )
        {
            $parent->removeChild( $element );
            $newParent->appendChild( $element );
            $ret['result'] =& $newParent;
        }
        elseif ( $parentName == 'paragraph' )
        {
            $newLine =& $this->createAndPublishElement( 'line', $ret );
            $parent->replaceChild( $newLine, $element );
            $newLine->appendChild( $element );
            $ret['result'] =& $newLine;
        }
        elseif ( $newParentName == 'paragraph' )
        {
            $newLine =& $this->createAndPublishElement( 'line', $ret );
            $parent->removeChild( $element );
            $newParent->appendChild( $newLine );
            $newLine->appendChild( $element );
            $ret['result'] =& $newLine;
        }
        elseif ( $this->XMLSchema->check( $parent, 'paragraph' ) )
        {
            $newLine =& $this->createAndPublishElement( 'line', $ret );
            $newPara =& $this->createAndPublishElement( 'paragraph', $ret );
            $parent->replaceChild( $newPara, $element );
            $newPara->appendChild( $newLine );
            $newLine->appendChild( $element );
            $ret['result'] =& $newLine;
        }
        return $ret;
    }

    // Structure handler for temporary <br> elements
    function &structHandlerBr( &$element, &$newParent )
    {
        $ret = array();
        if ( $newParent && $newParent->nodeName == 'line' )
        {
            $ret['result'] =& $newParent->parentNode;
        }
        return $ret;

    }

    // Structure handler for in-paragraph nodes.
    function &appendParagraph( &$element, &$newParent )
    {
        $ret = array();
        $parent =& $element->parentNode;
        if ( !$parent )
            return $ret;

        $parentName = $parent->nodeName;

        if ( $parentName != 'paragraph' )
        {
            if ( $newParent && $newParent->nodeName == 'paragraph' )
            {
                $parent->removeChild( $element );
                $newParent->appendChild( $element );
                return $newParent;
            }
            if ( $newParent && $newParent->parentNode && $newParent->parentNode->nodeName == 'paragraph' )
            {
                $para =& $newParent->parentNode;
                $parent->removeChild( $element );
                $para->appendChild( $element );
                return $newParent->parentNode;
            }

            if ( $this->XMLSchema->check( $parentName, 'paragraph' ) )
            {
                $newPara =& $this->createAndPublishElement( 'paragraph', $ret );
                $parent->replaceChild( $newPara, $element );
                $newPara->appendChild( $element );
                $ret['result'] =& $newPara;
            }
        }
        return $ret;
    }

    // Strucutre handler for #text
    function &structHandlerText( &$element, &$newParent )
    {
        $ret = null;
        $parent =& $element->parentNode;

        // Remove empty text elements
        if ( $element->content() == '' )
        {
            $parent->removeChild( $element );
            return $ret;
        }

        /*if ( !$newParent && $parent->nodeName == 'section' )
        {
            $parent->removeChild( $element );
        }*/

        $ret =& $this->appendLineParagraph( $element, $newParent );

        // Fix for italic/bold styles in Mozilla.
        $addStrong = $addEmph = null;
        $myParent =& $element->parentNode;
        while( $myParent )
        {
            $style = $myParent->getAttribute( 'style' );
            if ( $style && $addStrong !== false && strstr( $style, 'font-weight: bold;' ) )
            {
                $addStrong = true;
            }
            if ( $style && $addEmph !== false && strstr( $style, 'font-style: italic;' ) )
            {
                $addEmph = true;
            }

            if ( $myParent->nodeName == 'strong' )
            {
                $addStrong = false;
            }
            elseif ( $myParent->nodeName == 'emphasize' )
            {
                $addEmph = false;
            }
            elseif ( $myParent->nodeName == 'td' || $myParent->nodeName == 'th' || $myParent->nodeName == 'section' )
            {
                break;
            }
            $tmp =& $myParent;
            $myParent =& $tmp->parentNode;
        }

        $parent =& $element->parentNode;
        if ( $addEmph )
        {
            $emph =& $this->Document->createElement( 'emphasize' );
            $parent->insertBefore( $emph, $element );
            $parent->removeChild( $element );
            $emph->appendChild( $element );
        }
        if ( $addStrong )
        {
            $strong =& $this->Document->createElement( 'strong' );
            $parent->insertBefore( $strong, $element );
            $parent->removeChild( $element );
            $strong->appendChild( $element );
        }

        // Left trim spaces:
        if ( $this->TrimSpaces )
        {
            $trim = false;
            $currentElement =& $element;

            // Check if it is the first element in line
            do
            {
                $prev =& $currentElement->previousSibling();
                if ( $prev )
                    break;

                $currentElement =& $currentElement->parentNode;
                if ( $currentElement->nodeName == 'line' ||
                     $currentElement->nodeName == 'paragraph' )
                {
                    $trim = true;
                    break;
                }

            }while( $currentElement );

            if ( $trim )
            {
                // Trim and remove if empty
                $element->content = ltrim( $element->content );
                if ( $element->content == '' )
                {
                    $parent =& $element->parentNode;
                    $parent->removeChild( $element );
                }
            }
        }

        return $ret;
    }

    // Structure handler for 'header' tag.
    function &structHandlerHeader( &$element, &$param )
    {
        $ret = null;
        $parent =& $element->parentNode;
        $level = $element->getAttribute( 'level' );
        if ( !$level )
            $level = 1;

        $element->removeAttribute( 'level' );
        if ( $level )
        {
            $sectionLevel = -1;
            $current =& $element;
            while( $current->parentNode )
            {
                $tmp =& $current;
                $current =& $tmp->parentNode;
                if ( $current->nodeName == 'section' )
                    $sectionLevel++;
                else
                    if ( $current->nodeName == 'td' )
                    {
                        $sectionLevel++;
                        break;
                    }
            }
            if ( $level > $sectionLevel )
            {
                $newParent =& $parent;
                for ( $i = $sectionLevel; $i < $level; $i++ )
                {
                   $newSection =& $this->Document->createElement( 'section' );
                   if ( $i == $sectionLevel )
                       $newParent->insertBefore( $newSection, $element );
                   else
                       $newParent->appendChild( $newSection );
                   // Schema check
                   if ( !$this->processBySchemaTree( $newSection ) )
                   {
                       return $ret;
                   }
                   $newParent =& $newSection;
                   unset( $newSection );
                }
                $elementToMove =& $element;
                while( $elementToMove &&
                       $elementToMove->nodeName != 'section' )
                {
                    $next =& $elementToMove->nextSibling();
                    $parent->removeChild( $elementToMove );
                    $newParent->appendChild( $elementToMove );
                    $elementToMove =& $next;

                    if ( $elementToMove->nodeName == 'header' &&
                         $elementToMove->getAttribute( 'level' ) <= $level ) 
                        break;
                }
            }
            elseif ( $level < $sectionLevel )
            {
                $newLevel = $sectionLevel + 1;
                $current =& $element;
                while( $level < $newLevel )
                {
                    $tmp =& $current;
                    $current =& $tmp->parentNode;
                    if ( $current->nodeName == 'section' )
                        $newLevel--;
                }
                $elementToMove =& $element;
                while( $elementToMove &&
                       $elementToMove->nodeName != 'section' )
                {
                    $next =& $elementToMove->nextSibling();
                    $parent->removeChild( $elementToMove );
                    $current->appendChild( $elementToMove );
                    $elementToMove =& $next;

                    if ( $elementToMove->nodeName == 'header' &&
                         $elementToMove->getAttribute( 'level' ) <= $level ) 
                        break;
                }
            }
        }
        return $ret;
    }

    // Structure handler for 'custom' tag.
    function &structHandlerCustom( &$element, &$params )
    {
        $ret = null;
        $isInline = $this->XMLSchema->isInline( $element );
        if ( $isInline )
        {
            $ret =& $this->appendLineParagraph( $element, $params );

            $value = $element->getAttribute( 'value' );
            if ( $value )
            {
                $value = $this->washText( $value );
                $textNode = $this->Document->createTextNode( $value );
                $element->appendChild( $textNode );
            }
        }
        else
        {
            $ret =& $this->appendParagraph( $element, $params );
        }
        return $ret;
    }

    // Structure handler for 'ul' and 'ol' tags.
    function &structHandlerLists( &$element, &$params )
    {
        $ret = null;
        $parent =& $element->parentNode;
        $parentName = $parent->nodeName;

        if ( $parentName == 'paragraph' )
            return $ret;

        // If we are inside a list
        if ( $parentName == 'ol' || $parentName == 'ul' )
        {
            // If previous 'li' doesn't exist, create it,
            // else append to the previous 'li' element.
            $prev =& $element->previousSibling();
            if ( !$prev )
            {
                $li =& $this->Document->createElement( 'li' );
                $parent->insertBefore( $li, $element );
                $parent->removeChild( $element );
                $li->appendChild( $element );
            }
            else
            {
                $lastChild =& $prev->lastChild();
                if ( $lastChild->nodeName != 'paragraph' )
                {
                    $para =& $this->Document->createElement( 'paragraph' );
                    $parent->removeChild( $element );
                    $prev->appendChild( $element );
                }
                else
                {
                    $parent->removeChild( $element );
                    $lastChild->appendChild( $element );
                }
            }
        }
        if ( $parentName == 'li' )
        {
            $prev =& $element->previousSibling();
            if ( $prev )
            {
                $parent->removeChild( $element );
                $prev->appendChild( $element );
            }
        }
        $ret =& $this->appendParagraph( $element, $params );
        return $ret;
    }

    /*/ Structure handler for 'object' tag.
    function &structHandlerObject( &$element, &$params )
    {
        $ret = null;
        $parent =& $element->parentNode;
        if ( $parent->nodeName == 'link' )
        {
            $attr = $parent->getAttribute( 'id' );
            if ( $attr )
                $element->setAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'image:ezurl_id', $attr );

            $attr = $parent->getAttribute( 'target' );
            if ( $attr )
                $element->setAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'image:ezurl_target', $attr );

            $attr = $parent->getAttribute( 'href' );
            if ( $attr )
                $element->setAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'image:ezurl_href', $attr );

            $parent->removeChild( $element );
            $grandParent =& $parent->parentNode;
            $grandParent->insertBefore( $element, $parent );
            $grandParent->removeChild( $parent );
        }
        $ret =& $this->appendLineParagraph( $element, $params );
        return $ret;
    }*/

    // Structure handler for 'paragraph' element.
    function &structHandlerParagraph( &$element, &$params )
    {
        $ret = null;

        if ( $element->getAttribute( 'ezparser-new-element' ) == 'true' &&
             !$element->hasChildren() )
        {
            $element->parentNode->removeChild( $element );
            return $ret;
        }

        // Removes single line tag
        // php5 TODO: childNodes->length
        $line =& $element->lastChild();
        if ( count( $element->Children ) == 1 && $line->nodeName == 'line' )
        {
            $element->removeChild( $line );
            foreach( array_keys( $line->Children ) as $key )
            {
                $newChild =& $line->Children[$key];
                $line->removeChild( $newChild );
                $element->appendChild( $newChild );
            }   
        }

        return $ret;
    }

    /*
        Publish handlers. (called at pass 2)
    */
    // Publish handler for 'link' element.
    function &publishHandlerLink( &$element, &$params )
    {
        $ret = null;
		
        $href = $element->getAttribute( 'href' );
				
        if ( $href )
        {
            $objectID = false;
            if ( $this->eZPublishVersion > 3.5 &&
                 preg_match( "@^ezobject://([0-9]+)/?(#[^/]*)?/?@i", $href, $matches ) )
            {
                $objectID = $matches[1];
                if ( isset( $matches[2] ) )
                    $anchorName = substr( $matches[2], 1 );
                $element->setAttribute( 'object_id', $objectID );
            }
            elseif ( $this->eZPublishVersion > 3.5 &&
                     preg_match( "@^eznode://([^/#]+)/?(#[^/]*)?/?@i", $href, $matches ) )
            {
                $nodePath = $matches[1];
                if ( isset( $matches[2] ) )
                    $anchorName = substr( $matches[2], 1 );

                if ( ereg( "^[0-9]+$", $nodePath ) )
                {
                    $nodeID = $nodePath;
                    $node = eZContentObjectTreeNode::fetch( $nodeID );
                    if ( !$node )
                    {
                        $this->Messages[] = ezx18n( 'extension/ezdhtml', 'handlers/input', 'Node %1 does not exist.', false, array( $nodeID ) );
                    }
                }
                else
                {
                    $node = eZContentObjectTreeNode::fetchByURLPath( $nodePath );
                    if ( !$node )
                    {
                        $this->Messages[] = ezx18n( 'extension/ezdhtml', 'handlers/input', 'Node \'%1\' does not exist.', false, array( $nodePath ) );
                    }
                    else
                    {
                        $nodeID = $node->attribute( 'node_id' );
                    }
                    $element->setAttribute( 'show_path', 'true' );
                }

                if ( isset( $nodeID ) && $nodeID )
                {
                    $element->setAttribute( 'node_id', $nodeID );

                    $node = eZContentObjectTreeNode::fetch( $nodeID );
                    if ($node)
                        $objectID = $node->attribute( 'contentobject_id' );
                }
            }
            elseif ( $this->eZPublishVersion > 3.5 &&
                     ereg( "^#.*$" , $href ) )
            {
                $anchorName = substr( $href, 1 );
            }
            else
            {
                if ( $this->eZPublishVersion > 3.5 )
                {
                    $temp = explode( '#', $href );
                    $url = $temp[0];
                    if ( isset( $temp[1] ) )
                        $anchorName = $temp[1];
                }
                else
                    $url = $href;

                if ( $url )
                {
                    // Protection from XSS attack
                    if ( preg_match( "/^(java|vb)script:.*/i" , $url ) )
                    {
                        $this->isInputValid = false;
                        $this->Messages[] = "Using scripts in links is not allowed, link '$url' has been removed";
                        $element->removeAttribute( 'href' );
                        return $ret;
                    }

                    // Check mail address validity
                    if ( preg_match( "/^mailto:(.*)/i" , $url, $mailAddr ) )
                    {
                        include_once( 'lib/ezutils/classes/ezmail.php' );
                        if ( !eZMail::validate( $mailAddr[1] ) )
                        {
                            $this->isInputValid = false;
                            if ( $this->errorLevel >= 0 )
                                $this->Messages[] = ezi18n( 'kernel/classes/datatypes', "Invalid e-mail address: '%1'",
                                                            false, array( $mailAddr[1] ) );
                            $element->removeAttribute( 'href' );
                            return $ret;
                        }
                        
                    }
                    // Store urlID instead of href
                    $urlID = $this->convertHrefToID( $url );
                    if ( $urlID )
                    {
                        if ( $this->eZPublishVersion >= 3.6 )
                            $urlIDAttributeName = 'url_id';
                        else
                            $urlIDAttributeName = 'id';
                        $element->setAttribute( $urlIDAttributeName, $urlID );
                    }
                }
            }

            if ( $objectID && !in_array( $objectID, $this->linkedObjectIDArray ) )
                $this->linkedObjectIDArray[] = $objectID;

            if ( isset( $anchorName ) && $anchorName )
                    $element->setAttribute( 'anchor_name', $anchorName );
        }
        
        return $ret;
    }

    function convertHrefToID( $href )
    {
    	include_once('kernel/classes/datatypes/ezurl/ezurl.php');
        $href = str_replace("&amp;", "&", $href );

        $urlID = eZURL::registerURL( $href );

        if ( !in_array( $urlID, $this->urlIDArray ) )
             $this->urlIDArray[] = $urlID;

        return $urlID;
    }

    // Publish handler for 'table' element.
    function &publishHandlerTable( &$element, &$params )
    {
        $ret = null;

        // Trying to convert CSS rules to XML attributes
        // (for the case of pasting from external source)

        $style = $element->getAttribute( 'style' );
        if ( $style )
        {
            $styleArray = explode( ';', $style );
            foreach( $styleArray as $styleString )
            {
                if ( !$styleString )
                    continue;

                list( $styleName, $styleValue ) = explode( ':', $styleString );
                $styleName = trim( $styleName );
                $styleValue = trim( $styleValue );
                if ( $styleName )
                {
                    $element->setAttribute( $styleName, $styleValue );
                }
            }
        }
        return $ret;
    }

    // Publish handler for 'embed' element.
    function &publishHandlerEmbed( &$element, &$params )
    {
        $ret = null;
        $ID = $element->getAttribute( 'id' );
        if ( $ID )
        {
            $objectID = false;
            $element->removeAttribute( 'id' );
            if ( strstr( $ID, 'eZObject_' ) )
            {
                $objectID = substr( $ID, strpos( $ID, '_' ) + 1 );
                $element->setAttribute( 'object_id', $objectID );
            }
            if ( strstr( $ID, 'eZNode_' ) )
            {
                $nodeID = substr( $ID, strpos( $ID, '_' ) + 1 );
                $element->setAttribute( 'node_id', $nodeID );

                $node = eZContentObjectTreeNode::fetch( $nodeID );
                if ( $node )
                    $objectID = $node->attribute( 'contentobject_id' );
            }

            if ( $objectID && !in_array( $objectID, $this->embeddedObjectIDArray ) )
                $this->embeddedObjectIDArray[] = $objectID;
        }
        $align = $element->getAttribute( 'align' );
        if ( $align && $align == 'middle' )
        {
            $element->setAttribute( 'align', 'center' );
        }
        //$this->convertCustomAttributes( $element );
        return $ret;
    }

    /*/ Publish handler for 'object' element.
    function &publishHandlerObject( &$element, &$params )
    {
        $ret = null;
        $objectID = $element->getAttribute( 'id' );
        if ( $objectID )
        {
            $objectID = substr( $objectID, strpos( $objectID, '_' ) + 1 );
            $element->setAttribute( 'id', $objectID );
        }
        $align = $element->getAttribute( 'align' );
        if ( $align && $align == 'middle' )
        {
            $element->setAttribute( 'align', 'center' );
        }

        $href = $element->getAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'ezurl_href' );
        if ( $href )
        {
            $urlID = $this->convertHrefToID( $href );
            $element->setAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'image:ezurl_id', $urlID );
            $element->removeAttributeNS( 'http://ez.no/namespaces/ezpublish3/image/', 'ezurl_href' );
        }

        //$this->convertCustomAttributes( $element );
        return $ret;
    }*/

    function processAttributesBySchema( &$element )
    {
        // custom attributes conversion
        $attr = $element->getAttribute( 'customattributes' );
        if ( $attr )
        {
            $attrArray = explode( 'attribute_separation', $attr );
            foreach( $attrArray as $attr )
            {
                list( $attrName, $attrValue ) = explode( '|', $attr );
                $element->setAttributeNS( 'http://ez.no/namespaces/ezpublish3/custom/', 'custom:' . $attrName, $attrValue );
            }
        }

        parent::processAttributesBySchema( $element );
    }

    function getUrlIDArray()
    {
        return $this->urlIDArray;
    }

    function getEmbeddedObjectIDArray()
    {
        return $this->embeddedObjectIDArray;
    }

    function getLinkedObjectIDArray()
    {
        return $this->linkedObjectIDArray;
    }

    var $urlIDArray = array();
    var $embeddedObjectIDArray = array();
    var $linkedObjectIDArray = array();

    var $anchorAsAttribute = true;

    var $convertUnknownAttrsToCustom = false;
}
?>
