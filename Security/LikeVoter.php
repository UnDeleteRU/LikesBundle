<?php

namespace Undelete\LikesBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LikeVoter implements VoterInterface
{
    public function supportsAttribute($attribute)
    {
        return $attribute === 'like';
    }

    public function supportsClass($class)
    {
        return is_subclass_of($class, 'Undelete\LikesBundle\Entity\LikeableInterface');
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsAttribute($attributes[0])) {
            return VoterInterface::ACCESS_ABSTAIN;
        }


        if (!$this->supportsClass($object)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
